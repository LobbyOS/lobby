<?php
use \Fr\Process;

$appID = \H::i("id", null, "POST");
$output = array(
  "status" => null,
  "statusID" => null
);

if(!csrf()){
  $output = array(
    "statusID" => "error",
    "status" => "CSRF Token didn't match"
  );
}else if($appID === null){
  $output = array(
    "statusID" => "error",
    "status" => "Invalid App ID"
  );
}else{
  /**
   * A queue of App downloads
   */
  $appInstallQueue = getJSONOption("lobby_app_downloads");
  
  /**
   * If the $appID is in the queue, then give the download status of it
   * If the updated value is less than 20 seconds ago, then restart the download
   */
  if(isset($appInstallQueue[$appID]) && $appInstallQueue[$appID]["updated"] > strtotime("-20 seconds")){
    $output = array(
      "statusID" => $appInstallQueue[$appID]["statusID"],
      "status" => $appInstallQueue[$appID]["status"]
    );
  }else{
    $appInfo = \Lobby\Server::store(array(
      "get" => "app",
      "id" => $appID
    ));
    
    /**
     * App doesn't exist on Lobby Store
     */
    if($appInfo === "false"){
      $output = array(
        "status" => "error",
        "error" => "App Doesn't Exist"
      );
    }else{
      $appName = $appInfo["name"];
      
      $Process = new Process(Process::getPHPExecutable(), array(
        "arguments" => array(
          L_DIR . "/admin/ajax/install-app-bg.php",
          \Lobby::$lid,
          base64_encode(serialize($_SERVER)),
          $appID
        )
      ));
      /**
       * Get the command used to execute install-app-bg.php
       */
      $command = $Process->start();
      
      \Lobby::log("To install app '$appID', this command was executed : $command");
      
      $output = array(
        "statusID" => "download_intro",
        "status" => "Downloading <b>$appID</b>..."
      );
    }
  }
}
echo json_encode($output);
