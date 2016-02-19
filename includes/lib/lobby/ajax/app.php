<?php
require "../../../../load.php";
if(isset($_POST['cx74e9c6a45']) && H::csrf()){
  
  if(isset($_POST['s7c8csw91']) && $_POST['s7c8csw91'] !== ""){
    $AppID = $_POST['s7c8csw91']; // App ID
    $AjaxFile = urldecode($_POST['cx74e9c6a45']); // Ajax File Location
     
    $App = new \Lobby\Apps($AppID);
     
    if($App->exists && $App->isEnabled()){
      if($AjaxFile == ""){
        ser();
      }else{
        $AppClass = $App->run();
        $html = $AppClass->page("/ajax/$AjaxFile");
        if($html == "auto"){
          $html = $AppClass->inc("/src/ajax/$AjaxFile");
        }
        echo $html;
      }
    }
  }else{
    require_once L_DIR . $_POST['cx74e9c6a45'];
  }
}
