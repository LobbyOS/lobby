<?php
namespace Lobby;

use Lobby\Apps;
use Lobby\DB;
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
   * Check if updates are available
   */
  public static function isAvailable(){
    if(self::isCoreAvailable() || self::isAppsAvailable()){
      return true;
    }
  }

  public static function isCoreAvailable(){
    return version_compare(DB::getOption("lobby_latest_version"), \Lobby::getVersion(), ">");
  }

  public static function isAppsAvailable(){
    $apps = self::getApps();
    return !empty($apps);
  }

  public static function getApps(){
    return DB::getJSONOption("app_updates");
  }

  /**
   * Get the Zip File from Server & return back the downloaded file location
   */
  public static function zipFile($url, $zipFile){
    if( !extension_loaded('zip') ){
      self::log("PHP Zip extension is not installed.");
      throw new \Exception("Unable to install app, because <a href='". L_SERVER ."/docs/quick#section-requirements' target='_blank'>PHP Zip Extension</a> is not installed");
    }
    self::log("Started Downloading Zip File from {$url} to {$zipFile}");

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
        'follow_redirects' => true,
        'connect_timeout' => '20',
        'timeout' => 20000
      ));
    }catch(\Requests_Exception $error){
      self::log("HTTP Request Failed ($url) : $error");
      throw new \Exception("HTTP Request Failed : $error");
      return false;
    }
    self::log("Downloaded Zip File from {$url} to {$zipFile}");

    return $zipFile;
  }

  /**
   * Update The Lobby Core (Software)
   */
  public static function software(){
    if(\Lobby\Modules::exists("admin")){
      $admin_previously_installed = true;
    }

    $oldVer = self::$version;
    $latest_version = DB::getOption("lobby_latest_version");
    $url = Server::download("lobby", $latest_version);

    $zipFile = L_DIR . "/contents/update/" . $latest_version . ".zip";
    self::zipFile($url, $zipFile);

    // Make the Zip Object
    $zip = new \ZipArchive;
    if($zip->open($zipFile) != "true"){
      self::log("Unable to open downloaded Zip File.");
      throw new \Exception("Unable to open downloaded Zip File.");
    }

    self::log("Upgrading Lobby Software From {$zipFile}");
    /**
     * Extract New Version
     */
    $zip->extractTo(L_DIR);
    $zip->close();
    FS::remove($zipFile);

    self::finish_software_update(isset($admin_previously_installed));

    return L_URL . "/admin/settings.php?updated=1&oldver={$oldVer}" . \CSRF::getParam();
  }

  public static function finish_software_update($admin_previously_installed = false){
    FS::write("/upgrade.lobby", "1", "w");
    if($admin_previously_installed){
      FS::remove("/contents/modules/admin/disabled.txt");
    }

    $latest_version = DB::getOption("lobby_latest_version");
    self::log("Updated Lobby to version {$latest_version}");

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
            self::log("Removed Deprecated File: $fileLoc");
          }
        }
        copy(FS::loc($deprecatedFilesInfoLoc), FS::loc("$deprecatedFilesInfoLoc.txt"));
        FS::remove($deprecatedFilesInfoLoc);
        self::log("Finished Removing Deprecated Files");
      }
    }

    /**
     * Database Update
     */
    if(FS::exists("/update/sqlExecute.sql")){
      self::log("Upgrading Lobby Database");
      $sqlCode = FS::get("/update/sqlExecute.sql");
      $sql = \Lobby\DB::prepare($sqlCode);

      if(!$sql->execute()){
        throw new \Exception("Database update wasn't successful");
      }else{
        FS::remove("/update/sqlExecute.sql");
      }
      self::log("Updated Lobby Database");
    }

    FS::remove("/upgrade.lobby");

    self::log("Lobby is successfully Updated.");
  }

  /**
   * Update the App with the given ID
   */
  public static function app($id){
    if($id == ""){
      throw new \Exception("No app ID was passed");
    }

    $update = false;
    if(Apps::exists($id)){
      /**
       * This is an update of an existing app
       */
      $update = true;

      $App = new Apps($id);
      $oldVersion = $App->getInfo("version");

      self::log("Updating app '$id' to the latest version.");
    }else{
      self::log("Downloading and installing latest version of app '$id'");
    }

    $url = Server::download("app", $id);
    $zipFile = L_DIR . "/contents/update/{$id}.zip";

    self::zipFile($url, $zipFile);
    $zip = new \ZipArchive;

    if($zip->open($zipFile) != "true"){
      self::log("Unable to open downloaded app '$id' file : $zipFile");
      throw new Exception("Unable to open downloaded app file.");
    }else{
      /**
       * Extract App
       */
      $appDir = APPS_DIR . "/$id";
      if(FS::exists($appDir)){
        /**
         * Remove the contents of app directory
         */
        FS::remove($appDir, array(), false);
      }else{
        mkdir($appDir);
      }

      $zip->extractTo($appDir);
      $zip->close();

      /**
       * Do callback on app update
       */
      $App = new Apps($id);
      $AppObj = $App->getInstance();

      if($update)
        $AppObj->onUpdate($App->getInfo("version"), $oldVersion);
      else
        $AppObj->onUpdate($App->getInfo("version"));

      FS::remove($zipFile);

      self::log("Installed app {$id}");
      return true;
    }
  }
}
?>
