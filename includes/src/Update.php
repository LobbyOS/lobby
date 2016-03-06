<?php
namespace Lobby;

/**
 * The Update class.
 * For updating Lobby Core & Apps
 * The script execution time is set to unlimited
 */
set_time_limit(0);

class Update extends \Lobby {

  public static $progress = null;
  
  /**
   * Get the Zip File from Server & return back the downloaded file location
   */
  public static function zipFile($url, $zipFile){
    if( !extension_loaded('zip') ){
      \Lobby::log("Dependency Missing, Please install PHP Zip Extension");
      ser("PHP Zip Extension", "I can't find the Zip PHP Extension. Please Install It & Try again");
    }
    \Lobby::log("Started Downloading Zip File from {$url} to {$zipFile}");
    
    $userAgent = 'LobbyBot/0.1 (' . L_SERVER . ')';
    
    /**
     * Get The Zip From Server
     */
    $hooks = new \Requests_Hooks();
    if(self::$progress != null){
      $progress = self::$progress;
      $hooks->register('curl.before_send', function($ch) use($progress){
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progress);
      });
    }
    try {
      \Requests::get($url, array(
        "User-Agent" => $userAgent
      ), array(
        'filename' => $zipFile,
        'hooks' => $hooks,
        'timeout' => time()
      ));
    }catch(\Requests_Exception $error){
      \Lobby::log("HTTP Requests Error ($url) : $error");
      ser("Error", "HTTP Requests Error : " . $error);
      return false;
    }
    \Lobby::log("Downloaded Zip File from {$url} to {$zipFile}");
    
    return $zipFile;
  }
  
  /**
   * Update The Lobby Core (Software)
   */
  public static function software(){
    if(\Lobby\Modules::exists("admin")){
      $admin_previously_installed = true;
    }
    
    $oldVer = getOption("lobby_version");
    $latest_version = getOption("lobby_latest_version");
    $url = \Lobby\Server::download("lobby", $latest_version);
      
    $zipFile = L_DIR . "/contents/update/" . $latest_version . ".zip";
    self::zipFile($url, $zipFile);
    
    // Make the Zip Object
    $zip = new \ZipArchive;
    if($zip->open($zipFile) != "true"){
      \Lobby::log("Unable to open downloaded Zip File.");
      ser("Error", "Unable to open Zip File.  <a href='update.php'>Try again</a>");
    }
    
    \Lobby::log("Upgrading Lobby Software From {$zipFile}");
    /**
     * Extract New Version
     */
    $zip->extractTo(L_DIR);
    $zip->close();
    \Lobby\FS::remove($zipFile);
    
    self::finish_software_update(isset($admin_previously_installed));
    
    return L_URL . "/admin/settings.php?updated=1&oldver={$oldVer}" . \H::csrf("g");
  }
  
  public static function finish_software_update($admin_previously_installed = false){
    \Lobby\FS::write("/upgrade.lobby", "1", "w");
    if($admin_previously_installed){
      \Lobby\FS::remove("/contents/modules/admin/disabled.txt");
    }
    
    $latest_version = getOption("lobby_latest_version");
    \Lobby::log("Updated Lobby Software To version {$latest_version}");
 
    /**
     * Remove Depreciated Files
     */
    $deprecatedFilesInfoLoc = "/contents/update/removeFiles.php";
    if( \Lobby\FS::exists($deprecatedFilesInfoLoc) ){
      $files = \Lobby\FS::get($deprecatedFilesInfoLoc);
      $files = explode("\n", $files);
      
      if(count($files) !== 0){
        $files = array_filter($files);
        foreach($files as $file){
          $fileLoc = L_DIR . "/$file";
          if(file_exists($fileLoc) && $fileLoc != L_DIR){
            \Lobby\FS::remove($fileLoc);
            \Lobby::log("Removed Deprecated File: $fileLoc");
          }
        }
        copy(\Lobby\FS::loc($deprecatedFilesInfoLoc), \Lobby\FS::loc("$deprecatedFilesInfoLoc.txt"));
        \Lobby\FS::remove($deprecatedFilesInfoLoc);
        \Lobby::log("Finished Removing Deprecated Files");
      }
    }
 
    /**
     * Database Update
     */
    if(\Lobby\FS::exists("/update/sqlExecute.sql")){
      \Lobby::log("Upgrading Lobby Database");
      $sqlCode = \Lobby\FS::get("/update/sqlExecute.sql");
      $sql = \Lobby\DB::prepare($sqlCode);
      
      if(!$sql->execute()){
        ser("Error", "Database Update Couldn't be made. <a href='update.php'>Try again</a>");
      }else{
        \Lobby\FS::remove("/update/sqlExecute.sql");
      }
      \Lobby::log("Updated Lobby Database");
    }
    
    $oldVer = getOption("lobby_version");
    saveOption("lobby_version", $latest_version);
    saveOption("lobby_version_release", getOption("lobby_latest_version_release"));
    
    \Lobby\FS::remove("/upgrade.lobby");
    
    \Lobby::log("Lobby is successfully Updated.");
  }
  
  /**
   * Update the App with the given ID
   */
  public static function app($id){
    if($id == ""){
      ser("Error", "No App Mentioned to update.");
    }
    \Lobby::log("Installing Latest Version of App {$id}");
    
    $url = \Lobby\Server::download("app", $id);
    $zipFile = L_DIR . "/contents/update/{$id}.zip";
    self::zipFile($url, $zipFile);
 
    // Un Zip the file
    $zip = new \ZipArchive;
    if($zip->open($zipFile) != "true"){
      \Lobby::log("Unable to open Downloaded App ($id) File : $zipFile");
      ser("Error", "Unable to open Downloaded App File.");
    }else{
      /**
       * Extract App
       */
      $appDir = APPS_DIR . "/$id";
      if(!file_exists($appDir)){
        mkdir($appDir);
      }
      $zip->extractTo($appDir);
      $zip->close();
      
      \Lobby\FS::remove($zipFile);
      \Lobby::log("Installed App {$id}");
      return true;
    }
  }
}
?>
