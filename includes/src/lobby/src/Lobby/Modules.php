<?php
namespace Lobby;

use \Lobby\Apps;
use \Lobby\FS;

class Modules extends \Lobby {

  private static $core_modules = array(), $custom_modules = array(), $app_modules = array(), $modules = array();
  
  public static function __constructStatic(){
    
    self::$app_modules = self::appModules();
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
  
  /**
   * List modules in a directory
   */
  public static function dirModules($location){
    $location = L_DIR . $location;
    $modules = array_diff(scandir($location), array('..', '.'));
    $validModules = array();
    
    foreach($modules as $module){
      $loc = "$location/$module";
      if(self::valid($module, $loc)){
        $validModules[$module] = array(
          "id" => $module,
          "location" => $loc,
          "url" => L_URL . "/" . FS::rel($loc)
        );
      }
    }
    return $validModules;
  }
  
  /**
   * List App Modules
   */
  public static function appModules(){
    $modules = array();
    
    $apps = \Lobby\Apps::getApps();
    foreach($apps as $appID){
      $module_name = 'app_' . Apps::normalizeID($appID);
      $loc = APPS_DIR . "/$appID/module";
      
      if(self::valid($module_name, $loc)){
        $modules[$module_name] = array(
          "id" => $module_name,
          "appID" => $appID,
          "location" => $loc,
          "url" => L_URL . "/" . FS::rel($loc)
        );
      }
    }
    return $modules;
  }
  
  public static function load(){
    /**
     * $module = array(
     *   "id" => "The Module ID",
     *   "location" => "The absolute location to module",
     *   "url" => "URL to Module"
     * )
     */
    foreach(self::$modules as $module){
      require_once "{$module["location"]}/Module.php";
      $moduleIdentifier = "\Lobby\Module\\{$module['id']}";
      
      if(isset($module["appID"])){
        $App = new Apps($module["appID"]);
        
        new $moduleIdentifier(array(
          $module["id"],
          $module["location"],
          $module["url"]
        ), $App->getInstance());
      }else{
        new $moduleIdentifier(array(
          $module["id"],
          $module["location"],
          $module["url"]
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
      \Lobby\FS::write(self::$modules[$module]["location"] . "/disabled.txt", "1");
      return true;
    }else{
      return false;
    }
  }
  
}
