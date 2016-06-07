<?php
namespace Lobby;

use Lobby\FS;
use Lobby\Server;

/**
 * The Update class.
 * For updating Lobby Core & Apps
 */

class Update extends \Lobby {

  /**
   * cURL Progress callback should be inserted to here
   */
  public static $progress = null;
  
  /**
   * Get the Zip File from Server & return back the downloaded file location
   */
  public static function zipFile($url, $zipFile){
    if( !extension_loaded('zip') ){
      \Lobby::log("Dependency Missing, Please install PHP Zip Extension");
      echo ser("PHP Zip Extension", "I can't find the Zip PHP Extension. Please Install It & Try again");
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
      echo ser("Error", "HTTP Requests Error : " . $error);
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
    
    $oldVer = \Lobby::$version;
    $latest_version = getOption("lobby_latest_version");
    $url = Server::download("lobby", $latest_version);
      
    $zipFile = L_DIR . "/contents/update/" . $latest_version . ".zip";
    self::zipFile($url, $zipFile);
    
    // Make the Zip Object
    $zip = new \ZipArchive;
    if($zip->open($zipFile) != "true"){
      \Lobby::log("Unable to open downloaded Zip File.");
      echo ser("Error", "Unable to open Zip File.  <a href='update.php'>Try again</a>");
    }
    
    \Lobby::log("Upgrading Lobby Software From {$zipFile}");
    /**
     * Extract New Version
     */
    $zip->extractTo(L_DIR);
    $zip->close();
    FS::remove($zipFile);
    
    self::finish_software_update(isset($admin_previously_installed));
    
    return L_URL . "/admin/settings.php?updated=1&oldver={$oldVer}" . \H::csrf("g");
  }
  
  public static function finish_software_update($admin_previously_installed = false){
    FS::write("/upgrade.lobby", "1", "w");
    if($admin_previously_installed){
      FS::remove("/contents/modules/admin/disabled.txt");
    }
    
    $latest_version = getOption("lobby_latest_version");
    \Lobby::log("Updated Lobby to version {$latest_version}");
 
    /**
     * Remove Depreciated Files
     */
    $deprecatedFilesInfoLoc = "/contents/update/removeFiles.php";
    if( FS::exists($deprecatedFilesInfoLoc) ){
      $files = FS::get($deprecatedFilesInfoLoc);
      $files = explode("\n", $files);
      
      if(count($files) !== 0){
        $files = array_filter($files);
        foreach($files as $file){
          $fileLoc = L_DIR . "/$file";
          if(file_exists($fileLoc) && $fileLoc != L_DIR){
            FS::remove($fileLoc);
            \Lobby::log("Removed Deprecated File: $fileLoc");
          }
        }
        copy(FS::loc($deprecatedFilesInfoLoc), FS::loc("$deprecatedFilesInfoLoc.txt"));
        FS::remove($deprecatedFilesInfoLoc);
        \Lobby::log("Finished Removing Deprecated Files");
      }
    }
 
    /**
     * Database Update
     */
    if(FS::exists("/update/sqlExecute.sql")){
      \Lobby::log("Upgrading Lobby Database");
      $sqlCode = FS::get("/update/sqlExecute.sql");
      $sql = \Lobby\DB::prepare($sqlCode);
      
      if(!$sql->execute()){
        echo ser("Error", "Database Update Couldn't be made. <a href='update.php'>Try again</a>");
      }else{
        FS::remove("/update/sqlExecute.sql");
      }
      \Lobby::log("Updated Lobby Database");
    }
    
    FS::remove("/upgrade.lobby");
    
    \Lobby::log("Lobby is successfully Updated.");
  }
  
  /**
   * Update the App with the given ID
   */
  public static function app($id){
    if($id == ""){
      echo ser("Error", "No App Mentioned to update.");
    }
    \Lobby::log("Installing Latest Version of App {$id}");
    
    $url = Server::download("app", $id);
    $zipFile = L_DIR . "/contents/update/{$id}.zip";
    self::zipFile($url, $zipFile);
 
    // Un Zip the file
    if(class_exists("ZipArchive")){
      $zip = new \ZipArchive;
      if($zip->open($zipFile) != "true"){
        \Lobby::log("Unable to open Downloaded App ($id) File : $zipFile");
        echo ser("Error", "Unable to open Downloaded App File.");
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
        
        FS::remove($zipFile);
        \Lobby::log("Installed App {$id}");
        return true;
      }
    }else{
      throw new \Exception("Unable to Install App, because <a href='". L_SERVER ."/docs/quick#section-requirements' target='_blank'>PHP Zip Extension</a> is not installed");
    }
  }
}
?>
