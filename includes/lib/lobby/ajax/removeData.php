<?php
if( isset($_POST['appID']) && isset($_POST['key']) ){
   $app = $_POST['appID'];
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
