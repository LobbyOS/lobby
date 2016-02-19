<?php
require "../../../../load.php";
if( isset($_POST['appId']) && isset($_POST['key']) ){
   $app = $_POST['appId'];
   $key = $_POST['key'];
   
   if( !removeData($key, $app) ){
      echo "bad";
   }else{
      echo "good";
   }
}else{
   echo "fieldsMissing";
}
?>
