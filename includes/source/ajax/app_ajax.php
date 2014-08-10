<?php
require "../../load.php";
if( isset($_POST['s7c8csw91']) ){
 	/* We have this bizarre variables because we don't want duplicate variables that conflicts with each other */
 	$s7c8csw91	 = $_POST['s7c8csw91'];
 	$cx74e9c6a45 = urldecode($_POST['cx74e9c6a45']);
 	
 	define("APP_DIR", APPS_DIR . "/$s7c8csw91/");
 	define("APP_URL", L_HOST . "/app/$s7c8csw91");
 	
 	if($s7c8csw91 == "" || $cx74e9c6a45 == ""){
  		ser();
 	}else{
  		include(APP_DIR . "/$cx74e9c6a45");
 	}
}
?>