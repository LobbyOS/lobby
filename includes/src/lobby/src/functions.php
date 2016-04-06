<?php
/**
 * A Functions file that are simple aliases of class methods
 * Example :
 * \Lobby::getOption() can be used with just getOption()
 * 
 * Also contains Non Class functions
 */

/* Simple way to get value of an Option */
function getOption($key){
  if(\Lobby::$installed === false){
    return false;
  }else{
    return \Lobby\DB::getOption($key);
  }
}

/* Simple way to save an Option */
function saveOption($key, $value){
   if(!\Lobby::$installed){
      return false;
   }else{
      return \Lobby\DB::saveOption($key, $value);
   }
}

/* Show Error Messages */
function ser($title = "", $description="", $exit = false){
  \Lobby::ser($title, $description, $exit);
}

/* Show Success Messages */
function sss($title, $description){
  \Lobby::sss($title, $description);
}

/* Show Messages */
function sme($title, $description){
  \Lobby::sme($title, $description);
}

/* A map of $db->filt() that strips out HTML content */
function filt($string){
  return \Lobby\DB::filt( urldecode($string) );
}

/* Simple function to get Data Value */
function getData($key = "", $extra = false, $appID = ""){
  if( !\Lobby::$installed ){
    return false;
  }else{
    $appID = $appID == "" ? $GLOBALS['AppID'] : $appID;
    return \Lobby\DB::getData($appID, $key, $extra);
  }
}

/* Simple function to save Data */
function saveData($key = "", $value = "", $appID = ""){
  if(!\Lobby::$installed){
    return false;
  }else{
    $appID = $appID == "" ? $GLOBALS['AppID'] : $appID;
    return \Lobby\DB::saveData($appID, $key, $value);
  }
}

/* Simple Function to Remove Data */
function removeData($key = "", $appID = ""){
  if( !\Lobby::$installed ){
    return false;
  }else{
    $appID = $appID == "" ? $GLOBALS['AppID'] : $appID;
    return \Lobby\DB::removeData($appID, $key);
  }
}

function __($text, $domain = 'main'){
	return \Lobby\l10n::__($text, $domain);
}

function _e($text, $domain = 'main'){
	echo \Lobby\l10n::__($text, $domain);
}

function getJSONData($key){
  return \H::getJSONData($key);
}

function saveJSONData($key, $values){
  return \H::saveJSONData($key, $values);
}

function csrf($type){
  return \H::csrf($type);
}
