<?php
/**
 * \Lobby\Server
 * A Class for communication with Lobby server
 */
namespace Lobby;

class Server {

  public static $apiURL = null;
  
  public static function __constructStatic(){
    self::$apiURL = L_SERVER . "/api";
  }
  
  /**
   * Lobby Store
   */
  public static function store($data) {
    /**
     * Response is in JSON
     */
    $response = \Requests::post(self::$apiURL . "/apps", array(), $data)->body;
    if($response == "false"){
      return false;
    }else{
      $arr = json_decode($response, true);
      
      /**
       * Make sure the response was valid.
       */
      if(!is_array($arr)){
        \Lobby::log("Lobby Server Replied : {$response}");
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
    $apps = \Lobby\Apps::getApps();
    try {
      $response = \Requests::post($url, array(), array(
        "apps" => implode(",", $apps)
      ))->body;
    }catch (\Requests_Exception $error){
      \Lobby::log("Checkup with server failed ($url) : $error");
      $response = false;
    }
    if($response){
      
      $response = json_decode($response, true);
      if(is_array($response)){
        saveOption("lobby_latest_version", $response['version']);
        saveOption("lobby_latest_version_release", $response['released']);
        saveOption("lobby_latest_version_release_notes", $response['release_notes']);
    
        if(isset($response['apps']) && count($response['apps']) != 0){
          $AppUpdates = array();
          foreach($response['apps'] as $appID => $version){
            $App = new \Lobby\Apps($appID);
            if($App->info['version'] != $version){
              $AppUpdates[$appID] = $version;
            }
          }
          saveOption("app_updates", json_encode($AppUpdates));
        }
      }
    }
  }
  
}
