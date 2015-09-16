<?php
require "../../../../load.php";
if(isset($_POST['key']) && isset($_POST['value']) && H::csrf()){
   $key = urldecode($_POST['key']);
   $val = urldecode($_POST['value']);
   if(!saveOption($key, $val)){
      echo "bad";
   }else{
      echo "good";
   }
}else{
   echo "fieldsMissing";
}
?>
