<?php
namespace Lobby;

class Modules extends \Lobby {

  private static $required = array("panel"); // List of required modules by default
  private static $modules = array();
  
  public static function init(){
    self::$modules = array_merge(self::dirModules("/includes/lib/modules"), self::dirModules("/contents/modules"));
  }
  
  public static function get($type = "all"){
    if($type == "all"){
      return self::$modules;
    }elseif($type == "core"){
      $modules = self::dirModules("/includes/lib/modules");
    }elseif($type == "custom"){
      $modules = self::dirModules("/contents/modules");
    }
    return $modules;
  }
  
  public static function dirModules($location){
    $location = \Lobby\FS::loc($location);
    $modules = array_diff(scandir($location), array('..', '.'));
    $validModules = array();
    
    foreach($modules as $module){
      $loc = \Lobby\FS::loc("$location/$module");
      $disable = 0;
      if(file_exists("$loc/disabled.txt") && array_search($module, self::$required) === false){
        // Module Disabled
      }else{
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
