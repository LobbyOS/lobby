<?php
require "../../../../load.php";
if(isset($_POST['appID']) && isset($_POST['key']) && isset($_POST['value']) && H::csrf()){
   $app = $_POST['appID'];
   $key = $_POST['key'];
   $val = $_POST['value'];
   if(!saveData($key, $val, $app)){
      echo "bad";
   }else{
      echo "good";
   }
}else{
   echo "fieldsMissing";
}
?>
