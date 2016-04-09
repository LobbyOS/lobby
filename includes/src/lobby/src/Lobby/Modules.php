<?php
namespace Lobby;

use \Lobby\Apps;

class Modules extends \Lobby {

  private static $core_modules = array(), $custom_modules = array(), $app_modules = array(), $modules = array();
  
  public static function __constructStatic(){
    $apps = \Lobby\Apps::getApps();
    foreach($apps as $appID){
      $module_name = 'app_' . Apps::normalizeID($appID);
      $loc = APPS_DIR . "/$appID/module";
      if(self::valid($module_name, $loc)){
        self::$app_modules[$module_name] = array(
          "appID" => $appID,
          "location" => $loc
        );
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
    if(!file_exists("$loc/Module.php") || file_exists("$loc/disabled.txt")){
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
    /**
     * In case of App modules, $loc will be an array(
     *   "appID" => "The App ID",
     *   "loc" => "The path to Module.php folder"
     * )
     */
    foreach(self::$modules as $module => $loc){
      /**
       * If $loc is an array, it's an App Module
       */
      if(is_array($loc)){
        require_once "{$loc["location"]}/Module.php";
        $moduleIdentifier = "\Lobby\Module\\$module";
        
        $App = new Apps($loc["appID"]);
        
        new $moduleIdentifier(array(
          $loc["location"], L_URL . "/contents/modules/$module"
        ), $App->getInstance());
      }else{
        require_once "$loc/Module.php";
        $moduleIdentifier = "\Lobby\Module\\$module";
        
        new $moduleIdentifier(array(
          $loc,
          L_URL . "/contents/modules/$module"
        ));
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
  
  public static function disableModule($module){
    if(self::exists($module)){
      \Lobby\FS::write(self::$modules[$module] . "/disabled.txt", "1");
      return true;
    }else{
      return false;
    }
  }
  
}
