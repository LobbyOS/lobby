<?php
$action = Request::postParam("enable") === "true" ? "enable" : "disable";
$appID = Request::postParam("appID");

if($action !== null && $appID !== null && CSRF::check()){
  $App = new Lobby\Apps($appID);
  if(!$App->exists)
    Response::showError("Error", "I checked all over, but the app does not exist");

  if($action === "enable"){
    if($App->enableApp())
      echo "enable";
    else
      echo "enable-fail";
  }else if($action === "disable"){
    if($App->disableApp())
      echo "disable";
    else
      echo "disable-fail";
  }
}
