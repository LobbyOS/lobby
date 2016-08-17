<?php
/**
 * Lobby Modules Handler
 */

namespace Lobby;

use Lobby;
use Lobby\Apps;
use Lobby\FS;

/**
 * Modules extend the functionalities of Lobby
 */
class Modules extends \Lobby {

  /**
   * Core modules
   */
  private static $coreMods = array();
  
  /**
   * Custom modules
   */
  private static $customMods = array();
  
  /**
   * App modules
   */
  private static $appMods = array();
  
  /**
   * All modules combined into one array
   */
  private static $mods = array();
  
  /**
   * Get the list of modules and merge them.
   * 
   * There are 3 types of modules :
   * App Modules - Modules included in apps
   * Core Modules - Modules that comes preloaded with Lobby
   * Custom Modules - Modules in "contents/modules"
   */
  public static function __constructStatic(){
    self::$appMods = self::appModules();
    self::$coreMods = self::dirModules("/includes/lib/modules");
    self::$customMods = self::dirModules("/contents/modules");

    self::$mods = array_merge(self::$coreMods, self::$customMods, self::$appMods);
  }
  
  /**
   * Get the list of modules
   * @param string $type Type of modules to get
   * @see self::__constructStatic()
   */
  public static function get($type = "all"){
    if($type == "all"){
      return self::$mods;
    }elseif($type == "core"){
      return self::$coreMods;
    }elseif($type == "custom"){
      return self::$customMods;
    }elseif($type == "app"){
      return self::$appMods;
    }
  }
  
  /**
   * Check whether a module is valid
   * @param string $module Module's ID
   * @param string $loc The location of the module
   * @return bool Whether the module is valid or not
   */
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
   * @param string $location Path to directory
   * @return array Modules
   */
  public static function dirModules($location){
    $location = FS::loc($location);
    $mods = array_diff(scandir($location), array('..', '.'));
    $validModules = array();
    
    foreach($mods as $module){
      $loc = "$location/$module";
      if(self::valid($module, $loc)){
        $validModules[$module] = array(
          "id" => $module,
          "location" => $loc,
          "url" => Lobby::getURL() . "/" . FS::rel($loc)
        );
      }
    }
    return $validModules;
  }
  
  /**
   * List App Modules
   */
  public static function appModules(){
    $mods = array();
    
    $apps = Apps::getEnabledApps();
    foreach($apps as $appID){
      $module_name = 'app_' . Apps::normalizeID($appID);
      $loc = Apps::getAppsDir() . "/$appID/module";
      
      if(self::valid($module_name, $loc)){
        $mods[$module_name] = array(
          "id" => $module_name,
          "appID" => $appID,
          "location" => $loc,
          "url" => Lobby::getURL() . "/" . FS::rel($loc)
        );
      }
    }
    return $mods;
  }
  
  /**
   * Run all loaded modules
   */
  public static function load(){
    /**
     * $module = array(
     *   "id" => "The Module ID",
     *   "location" => "The absolute location to module",
     *   "url" => "URL to Module"
     * )
     */
    foreach(self::$mods as $module){
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
  
  /**
   * Whether a module exists
   * @param string $module Module's ID
   */
  public static function exists($module){
    if(isset(self::$mods[$module]) !== false){
      return true;
    }else{
      return false;
    }
  }
  
  /**
   * Disable a module by making a "disabled.txt" file in the location
   * @param string $module Module's ID
   */
  public static function disableModule($module){
    if(self::exists($module)){
      \Lobby\FS::write(self::$mods[$module]["location"] . "/disabled.txt", "1");
      return true;
    }else{
      return false;
    }
  }
  
}
