<?php
require "../../../../load.php";
$data = file_get_contents(L_SERVER . "/ping");
if($data != ""){
  echo "true";
}else{
  echo "false";
}
?>
