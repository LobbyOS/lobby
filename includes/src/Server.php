<?php
/**
 * \Lobby\Server
 * Class for communication with server
 */
namespace Lobby;

class Server {
  
  /**
   * Lobby Store functions
   */
  public static function Store($data) {
    /**
     * Response is in JSON
     */
    $response = \Lobby::loadURL(L_SERVER . "/apps", $data, "POST");
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
        return count($arr) == 1 ? $arr[0] : $arr;
      }
    }
  }
  
  public static function download($type = "app", $id){
    $url = "";
    if($type == "app"){
      $url = L_SERVER . "/download/app/{$id}";
    }elseif($type == "lobby"){
      $url = L_SERVER . "/download/lobby/{$id}";
    }
    return $url;
  }
  
  /**
   * Get updates
   */
  public static function check(){
    $response = \Lobby::loadURL(L_SERVER . "/updates", array(
      "apps" => implode(",", \Lobby\Apps::getApps())
    ), "POST");
    if($response){
      
      $response = json_decode($response, true);
      if(is_array($response)){
        saveOption("lobby_latest_version", $response['version']);
        saveOption("lobby_latest_version_release", $response['released']);
    
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
