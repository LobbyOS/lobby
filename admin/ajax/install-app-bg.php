<?php
/**
 * Argument #1 should equal the Lobby Unique ID
 * Argument #2 is $_SERVER variable
 * Argument #3 is App ID to install
 */

if(isset($argv[2])){
  $_SERVER = unserialize(base64_decode($argv[2]));
  require __DIR__ . "/../../load.php";
}else{
  exit;
}

use \Lobby\Apps;
use \Lobby\FS;
use \Lobby\Update;

if($argv[1] === \Lobby::getLID() && isset($argv[3])){
  $appID = $argv[3];
  
  function sendStatusToLobby($statusID, $status){
    global $appID;
    
    Lobby\DB::saveJSONOption("lobby_app_downloads", array(
      $appID => array(
        "statusID" => $statusID,
        "status" => $status,
        "updated" => time()
      )
    ));
  }
  
  /**
   * Record the last percentage of data downloaded
   * This to know whether download has progressed from previous state
   */
  $lastPercentage = 0;
  
  \Lobby\Update::$progress = function($resource, $download_size, $downloaded, $upload_size, $uploaded = "") use($appID, $lastPercentage){
    /**
     * On new versions of cURL, $resource parameter is not passed
     * So, swap vars if it doesn't exist
     */
    if(!is_resource($resource)){
      $uploaded = $upload_size;
      $upload_size = $downloaded;
      $downloaded = $download_size;
      $download_size = $resource;
    }
    
    if($download_size > 1000 && $downloaded > 0){
      $percent = round($downloaded / $download_size  * 100, 0);
    }else{
      $percent = 1;
    }
    
    if($lastPercentage !== $percent || isset($GLOBALS['non_percent'])){
      $lastPercentage = $percent;
      
      if($download_size > 0){
        $readable_size = FS::normalizeSize($download_size);
        sendStatusToLobby("download_status", "Downloaded $percent% of $readable_size");
      }else{
        /**
         * We couldn't find the percentage
         */
        $GLOBALS['non_percent'] = 1;
        
        $downloaded = FS::normalizeSize($downloaded);
        sendStatusToLobby("download_status", "Downloaded $downloaded");
      }
      
      /**
       * Show Install message when download is completed
       */
      if($percent == 100 && !isset($GLOBALS['install-msg-printed'])){
        $GLOBALS['install-msg-printed'] = 1;
        $downloaded = FS::normalizeSize($downloaded);
        sendStatusToLobby("download_status", "Downloaded 100% of $downloaded");
        sleep(2);
        sendStatusToLobby("install_status", "Installing <b>$appID</b>...");
        sleep(2);
      }
    }
  };
  
  try{
    /**
     * Update::app() will only return TRUE if download is completed
     */
    if(Update::app($appID)){
      $App = new Apps($appID);
      $App->enableApp();
      
      sendStatusToLobby("install_finished", "Installed <b>$appID</b>.<cl/><a href='". $App->info["url"] ."' class='btn green'>Open App</a>");
    }
  }catch(\Exception $e){
    sendStatusToLobby("error", $e->getMessage());
  }
}
