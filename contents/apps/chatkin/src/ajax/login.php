<?php
if(isset($_POST['network']) && isset($_POST['username']) && isset($_POST['password'])){
  $network = $_POST['network'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  
  require_once APP_DIR . "/src/inc/class.chat.php";
  if($network == "facebook"){
    $class = new chatkin\Facebook();
    echo $class->login($username, $password) == true ? 1 : 0;
  }
}
