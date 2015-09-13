<?php
namespace Lobby;

class Modules extends \Lobby {

  private static $required = array("panel"); // List of required modules by default
  private static $core_modules = array(), $custom_modules = array(), $app_modules = array(), $modules = array();
  
  public static function init(){
    $apps = \Lobby\Apps::getApps();
    foreach($apps as $app => $null){
      $module_name = 'apps/' . $app . '/module';
      $loc = APPS_DIR . "/$app/module";
      if(self::valid($module_name, $loc)){
        self::$app_modules[$module_name] = $loc;
      }
    }
    
    self::$core_modules = self::dirModules("/includes/lib/modules");
    self::$custom_modules = self::dirModules("/contents/modules");

    self::$modules = array_merge(self::$core_modules, self::$custom_modules, self::$app_modules);
  }
  
  public static function get($type = "all"){
    if($type == "all"){
      return self::$modules;
    }elseif($type == "core"){
      return self::$core_modules;
    }elseif($type == "custom"){
      return self::$custom_modules;
    }elseif($type == "app"){
      return self::$app_modules;
    }
  }
  
  public static function valid($module, $loc){
    if(!file_exists("$loc/load.php") || (file_exists("$loc/disabled.txt") && array_search($module, self::$required) === false)){
      // Module Disabled or not valid
      return false;
    }else{
      return true;
    }
  }
  
  public static function dirModules($location){
    $location = L_DIR . $location;
    $modules = array_diff(scandir($location), array('..', '.'));
    $validModules = array();
    
    foreach($modules as $module){
      $loc = "$location/$module";
      if(self::valid($module, $loc)){
        $validModules[$module] = $loc;
      }
    }
    return $validModules;
  }
  
  public static function load(){
    foreach(self::$modules as $module => $loc){
      if(file_exists("$loc/load.php")){
        require_once "$loc/load.php";
      }
    }
  }
  
  public static function exists($module){
    if(isset(self::$modules[$module]) !== false){
      return true;
    }else{
      return false;
    }
  }
}
\Lobby\Modules::init();
