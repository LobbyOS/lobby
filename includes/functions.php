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

/* Show Error Messages */
function ser($title="", $description="", $exit = true){
 	L::ser($title, $description, $exit);
}

/* Show Success Messages */
function sss($title, $description){
 	L::sss($title, $description);
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