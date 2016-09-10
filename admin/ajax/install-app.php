<?php
use \Fr\Process;

$appID = \Request::postParam("id");

if(!CSRF::check()){
  echo json_encode(array(
    "statusID" => "error",
    "status" => "CSRF Token didn't match"
  ));
}else if($appID === null){
  echo json_encode(array(
    "statusID" => "error",
    "status" => "Invalid App ID"
  ));
}else{
  /**
   * A queue of App downloads
   */
  $appInstallQueue = Lobby\DB::getJSONOption("lobby_app_downloads");

  /**
   * If the $appID is in the queue, then give the download status of it
   * If the updated value is less than 20 seconds ago, then restart the download
   */
  if(isset($appInstallQueue[$appID]) && $appInstallQueue[$appID]["updated"] > strtotime("-35 seconds")){
    echo json_encode(array(
      "statusID" => $appInstallQueue[$appID]["statusID"],
      "status" => $appInstallQueue[$appID]["status"]
    ));
  }else{
    $appInfo = \Lobby\Server::store(array(
      "get" => "app",
      "id" => $appID
    ));

    /**
     * App doesn't exist on Lobby Store
     */
    if($appInfo === "false"){
      echo json_encode(array(
        "status" => "error",
        "error" => "App Doesn't Exist"
      ));
    }else{
      $appName = $appInfo["name"];

      $Process = new Process(Process::getPHPExecutable(), array(
        "arguments" => array(
          L_DIR . "/admin/ajax/install-app-bg.php",
          \Lobby::getLID(),
          base64_encode(serialize($_SERVER)),
          $appID
        )
      ));

      /**
       * Get the command used to execute install-app-bg.php
       */
      $command = $Process->start(function() use ($appID){
        /**
         * This callback will close the connection between browser and server,
         * http://stackoverflow.com/q/36968552/1372424
         */
        echo json_encode(array(
          "statusID" => "download_intro",
          "status" => "Downloading <b>$appID</b>..."
        ));
      });

      \Lobby::log("To install app '$appID', this command was executed : $command");
    }
  }
}
