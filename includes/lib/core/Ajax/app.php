<?php
require "../../../../load.php";
if(isset($_POST['s7c8csw91']) && isset($_POST['cx74e9c6a45'])){
  
  $AppID = $_POST['s7c8csw91']; // App ID
  $AjaxFile = urldecode($_POST['cx74e9c6a45']); // Ajax File Location
   
  $App = new \Lobby\Apps($AppID);
   
  if($App->exists && $App->isEnabled()){
    if($AjaxFile == ""){
      ser();
    }else{
      $AppClass = $App->run();
      echo $AppClass->inc("/src/Ajax/$AjaxFile");
    }
  }
}
?>
