<?php
$file = \Request::postParam('cx74e9c6a45', '');
$appID = \Request::postParam('s7c8csw91', '');

if($file != "" && CSRF::check()){
  if($appID !== ""){
    $App = new \Lobby\Apps($appID);

    if($App->exists && $App->enabled){
      $AppObj = $App->getInstance();
      echo $AppObj->getARResponse($file);
    }
  }else{
    if(\Lobby\FS::exists($file)){
      require_once \Lobby\FS::loc($file);
    }else{
      echo "fileNotFound";
    }
  }
}
