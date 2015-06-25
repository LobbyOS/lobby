<?php
namespace Lobby;
/**
 * The Update class.
 * For updating Lobby Core & Apps
 */

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
    
    // Get The Zip From Server
    $ch = curl_init();
    $zipResource = fopen($zipFile, "w");
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if(self::$progress != null){
      curl_setopt($ch, CURLOPT_NOPROGRESS, false);
      curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, self::$progress);
    }
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_FILE, $zipResource);
    
    $page = curl_exec($ch);
    if(!$page) {
      $error = curl_error($ch);
      \Lobby::log("cURL Error ($url) : $error");
      ser("Error", "cURL Error : " . $error);
      return false;
    }
    curl_close($ch);
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
    
    $latest_version = getOption("lobby_latest_version");
    $url = \Lobby\Server::download("lobby", $latest_version);
      
    $zipFile = "/contents/update/" . $latest_version . ".zip";
    $zipFile = \Lobby\FS::loc($zipFile);
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
    
    if(isset($admin_previously_installed)){
      \Lobby\FS::remove("/contents/modules/admin/disabled.txt");
    }
    \Lobby::log("Updated Lobby Software To version {$latest_version}");
 
    /* Remove Depreciated Files */
    if( \Lobby\FS::exists("/contents/update/removeFiles.php") ){
      $files = \Lobby\FS::get("/contents/update/removeFiles.php");
      $files = explode("\n", $files);
      
      if(count($files) != 0){
        foreach($files as $file){ // iterate files
          $fileLoc = \Lobby\FS::loc("/$file");
          if(file_exists($fileLoc) && $fileLoc != L_DIR){
            $type = filetype($fileLoc);
            if($type == "file"){
              \Lobby\FS::remove($fileLoc);
            }
          }
        }
        \Lobby\FS::remove(L_DIR . "/contents/update/removeFiles.php");
        \Lobby::log("Removed Deprecated Files");
      }
    }
 
    /* Database */
    if(\Lobby\FS::exists("/update/sqlExecute.sql")){
      \Lobby::log("Upgrading Lobby Database");
      $sqlCode = \Lobby\FS::get("/update/sqlExecute.sql");
      $sql = \Lobby\DB::prepare($sqlCode);
      if( !$sql->execute() ){
       ser("Error", "Database Update Couldn't be made. <a href='update.php'>Try again</a>");
      }else{
       \Lobby\FS::remove("/update/sqlExecute.sql");
      }
      \Lobby::log("Updated Lobby Database");
    }
    
    $oldVer = getOption("lobby_version");
    saveOption( "lobby_version", getOption("lobby_latest_version") );
    saveOption( "lobby_version_release", getOption("lobby_latest_version_release") );
    \Lobby::log("Lobby is successfully Updated.");
    
    return L_URL . "/admin/about.php?updated=1&oldver={$oldVer}" . \H::csrf("g");
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
    $zipFile = \Lobby\FS::loc("/contents/update/{$id}.zip");
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
      $zip->extractTo(APPS_DIR);
      $zip->close();
      
      \Lobby\FS::remove($zipFile);
      \Lobby::log("Installed App {$id}");
      return true;
    }
  }
}
?>
