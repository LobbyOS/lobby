<?php
namespace Lobby;

use vierbergenlars\SemVer\version;
use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\SemVerException;
use Lobby\Apps;

/**
 * A class for satisying depenedencies of an App
 * "Need" is a synonym of Require
 */
class Need {

  /**
   * Get Version of a component
   */
  public static function getDependencyVersion($dependency){
    /**
     * If dependency is 'app/admin' etc.
     */
    if(strpos($dependency, "/") !== false){
      list($dependency, $subDependency) = explode("/", $dependency);
    }
    switch($dependency){
      case "lobby":
        return \Lobby::$version;
        break;
      case "app":
        $App = new Apps($subDependency);
        return $App->exists ? $App->info["version"] : 0;
        break;
      case "curl":
        $curl = function_exists("curl_version") ? curl_version() : 0;
        return $curl === 0 ? 0 : $curl["version"];
        break;
      default:
        /**
         * phpversion() returns FALSE on failure
         */
        $v = phpversion($dependency);
        return $v ? $v : 0;
    }
    
  }
  
  /**
   * Check requirements
   * @param array $requires The array containing the requirements
   * @param bool $boolean Whether the return value must be a boolean
   */
  public static function checkRequirements($requires, $boolean = false){
    $result = $requires;
    /**
     * $requiredVersion will look like ">=5.0"
     */
    foreach($requires as $dependency => $requiredVersion){
      $currentVersion = self::getDependencyVersion($dependency);
      
      /**
       * Compare the current version and required version
       */
      $version = new version($currentVersion);
      if($version->satisfies(new expression($requiredVersion))){
        $result[$dependency] = true;
      }else{
        $result[$dependency] = false;
      }
    }
    return $boolean ? !in_array(false, $result) : $result;
  }

}
