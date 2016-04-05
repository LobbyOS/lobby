<?php
namespace Lobby;

class Modules extends \Lobby {

  private static $core_modules = array(), $custom_modules = array(), $app_modules = array(), $modules = array();
  
  public static function init(){
    $apps = \Lobby\Apps::getApps();
    foreach($apps as $app){
      $module_name = 'app_' . str_replace("-", "_", $app);
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
    foreach(self::$modules as $module => $loc){
      require_once "$loc/Module.php";
      $moduleIdentifier = "\Lobby\Module\\$module";
      $Module = new $moduleIdentifier(array(
        $loc, L_URL . "/contents/modules/$module"
      ));
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
\Lobby\Modules::init();
