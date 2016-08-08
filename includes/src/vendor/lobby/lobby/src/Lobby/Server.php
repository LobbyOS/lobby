<?php
/**
 * \Lobby\Server
 * A Class for communication with Lobby server
 */
namespace Lobby;

use Lobby\Apps;
use Lobby\UI\Panel;

class Server {

  public static $apiURL = null;
  
  public static function __constructStatic(){
    self::$apiURL = L_SERVER . "/api";
  }
  
  /**
   * Append Lobby Info to POST data
   */
  public static function makeData($data){
    return array_replace_recursive(array(
      "lobby" => array(
        "lid" => \Lobby::getLID(),
        "version" => \Lobby::$version
      )
    ), $data);
  }
  
  /**
   * Lobby Store
   */
  public static function store($data) {
    /**
     * Response is in JSON
     */
    try{
      $response = \Requests::post(self::$apiURL . "/apps", array(), self::makeData($data))->body;
    }catch(\Requests_Exception $error){
      \Lobby::log("HTTP Request Failed ($url) : $error");
      echo ser("HTTP Request Failed", $error);
      return false;
    }
    
    if($response === "false"){
      return false;
    }else{
      $arr = json_decode($response, true);
      
      /**
       * Make sure the response was valid.
       */
      if(!is_array($arr)){
        \Lobby::log("HTTP Request Failed ($url) : Lobby server replied stupid data - $response");
        return false;
      }else{
        return $arr;
      }
    }
  }
  
  /**
   * Download Zip files
   */
  public static function download($type = "app", $id){
    $url = "";
    if($type === "app"){
      $url = self::$apiURL . "/app/{$id}/download";
    }elseif($type === "lobby"){
      $url = self::$apiURL . "/lobby/download/{$id}";
    }
    return $url;
  }
  
  
  /**
   * Get updates
   */
  public static function check(){
    $url = self::$apiURL . "/lobby/updates";
    $apps = Apps::getApps();
    try {
      $response = \Requests::post($url, array(), self::makeData(array(
        "apps" => implode(",", $apps)
      )))->body;
    }catch (\Requests_Exception $error){
      \Lobby::log("Checkup with server failed ($url) : $error");
      $response = false;
    }
    
    if($response){
      $response = json_decode($response, true);
      if(is_array($response)){
        DB::saveOption("lobby_latest_version", $response['version']);
        DB::saveOption("lobby_latest_version_release", $response['released']);
        DB::saveOption("lobby_latest_version_release_notes", $response['release_notes']);
    
        if(isset($response['apps']) && count($response['apps']) != 0){
          $AppUpdates = array();
          foreach($response['apps'] as $appID => $version){
            $App = new \Lobby\Apps($appID);
            if($App->hasUpdate($version)){
              $AppUpdates[$appID] = $version;
            }
          }
          DB::saveOption("app_updates", json_encode($AppUpdates));
        }
        
        if(isset($response["notify"])){
          foreach($response["notify"]["items"] as $itemID => $item){
            if(isset($item["href"])){
              $item["href"] = \Lobby::u($item["href"]);
            }
            Panel::addNotifyItem("lobby_server_msg_" . $itemID, $item);
          }
          
          foreach($response["notify"]["remove_items"] as $itemID){
            Panel::removeNotifyItem("lobby_server_msg_" . $itemID);
          }
        }
      }
    }
  }
  
}
