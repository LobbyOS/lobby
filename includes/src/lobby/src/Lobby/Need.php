<?php
namespace Lobby;

use vierbergenlars\SemVer\version;
use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\SemVerException;

/**
 * A class for satisying depenedencies of an App
 * "Need" is a synonym of Require
 */

class Need {

  /**
   * Get Version of a component
   */
  public static function getDependencyVersion($dependency){
    
    switch($dependency){
      case "lobby":
        return \Lobby::$version;
        break;
      case "curl":
        return function_exists("curl_version") ? curl_version() : 0;
        break;
      default:
        return 0;
    }
    
  }
  
  /**
   * Check requirements
   * $requires is array()
   * $boolean - To tell whether the return value must be a boolean or not
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
    return $boolean ? in_array(false, $result) : $result;
  }

}
