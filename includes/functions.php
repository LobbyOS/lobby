<?php
/* A Functions file that are simple duplicates of long functions of classes
 * Example :
 * $LS->getOption() can be used with just getOption()
 * ------
 * Also contains functions that are not associated with class (not duplicates)
 * ------
*/

/* Simple way to get value of an Option */
function getOption($key){
 	if(!$GLOBALS['db']->db){
  		return false;
 	}else{
  		return $GLOBALS['db']->getOption($key);
 	}
}

/* Simple way to save an Option */
function saveOption($key, $value){
 	if(!$GLOBALS['db']->db){
  		return false;
 	}else{
  		return $GLOBALS['db']->saveOption($key, $value);
 	}
}

/* The current File */
function curFile(){
 	$parts = explode("/", $_SERVER['SCRIPT_FILENAME']);
 	return $parts[ count($parts)-1 ];
}

/* Show Error Messages */
function ser($title="", $description="", $exit = true){
 	$html = "";
 	if($title == ''){
  		/* If no Title, give a 404 Page */
  		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
  		include(L_ROOT . "/includes/source/error.php");
  		exit;
 	}else{
  		$html .= "<div class='message'>";
			$html .= "<div style='color:red;' class='title'>$title</div>";
			if($description != ""){
				$html .= "<div style='color:red;'>$description</div>";
			}
		$html .= "</div>";
 	}
 	echo $html;
 	if($exit){
  		exit;
  	}
}

/* Show Success Messages */
function sss($title, $description){
 	$html = "<div class='message'>";
		if($title == ""){
			$html .= "<div style='color:green;' class='title'>Success</div>";
		}else{
			$html .= "<div style='color:green;' class='title'>$title</div>";
		}
		if($description != ""){
			$html .= "<div style='color:green;'>$description</div>";
		}
	$html .= "</div>";
 	echo $html;
}

/* A map of $db->filt() that strips out HTML content */
function filt($string){
 	return $GLOBALS['db']->filt( urldecode($string) );
}

/* Simple function to get Data Value */
function getData($appID, $key=""){
 	if( !$GLOBALS['db']->db ){
  		return false;
 	}else{
  		return $GLOBALS['db']->getData($appID, $key);
 	}
}

/* Simple function to save Data */
function saveData($appID, $key = "", $value = ""){
 	if(!$GLOBALS['db']->db){
  		return false;
 	}else{
  		return $GLOBALS['db']->saveData($appID, $key, $value);
 	}
}

/* Simple Function to Remove Data */
function removeData($appID, $key = ""){
 	if( !$GLOBALS['db']->db ){
  		return false;
 	}else{
  		return $GLOBALS['db']->removeData($appID, $key);
 	}
}
?>