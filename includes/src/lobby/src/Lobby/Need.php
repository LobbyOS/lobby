<?php
/**
 * Lobby\Need
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/Module.php
 */

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
   * Get version of a dependency
   * @param string $dependency The dependency
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
        return \Lobby::getVersion();
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
   * @param bool $multi Whether return value contain both satisfy boolean and requirement version
   */
  public static function checkRequirements($requires, $boolean = false, $multi = false){
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

        /**
         * If boolean value is needed and version
         * doesn't satisfy, skip later tests
         */
        if($boolean)
          continue;
      }

      /**
       * If dependency is an app
       */
      if(strpos($dependency, "/") !== false){
        list($mainDependency, $subDependency) = explode("/", $dependency);
        if($mainDependency === "app"){
          $App = new Apps($subDependency);

          if(!$App->exists){
            $result[$dependency] = false;
          }else if($multi){
            $result = $result + self::checkRequirements($App->info["require"], false, true);
          }else if($boolean){
            $result[$dependency] = self::checkRequirements($App->info["require"], true);
          }else{
            $result = $result + self::checkRequirements($App->info["require"]);
          }
        }
      }
    }

    if($multi){
      foreach($result as $dependency => $satisfy){
        if(!is_array($satisfy)){
          $result[$dependency] = array(
            "require" => $requires[$dependency],
            "satisfy" => $satisfy
          );
        }
      }
      return $result;
    }
    return $boolean ? !in_array(false, $result) : $result;
  }

}
