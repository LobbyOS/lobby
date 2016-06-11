<?php
require "../../../../load.php";

$file = \Request::postParam('cx74e9c6a45', '');
$appID = \Request::postParam('s7c8csw91', '');

if($file != "" && CSRF::check()){
  if($appID !== ""){
    $App = new \Lobby\Apps($appID);
     
    if($App->exists && $App->enabled){
      $AppClass = $App->run();
      $html = $AppClass->page("/ajax/$file");
      if($html === "auto"){
        $html = $AppClass->inc("/src/ajax/$file");
      }
      echo $html;
    }
  }else{
    if(\Lobby\FS::exists($file)){
      require_once \Lobby\FS::loc($file);
    }else{
      echo "fileNotFound";
    }
  }
}
