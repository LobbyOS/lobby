<?php
/**
 * CSRF
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/CSRF.php
 */

/**
 * Protect from CSRF attacks
 */
class CSRF {

  /**
   * Current session's CSRF token
   */
  protected static $token = null;

  /**
   * Create a CSRF token and set it as a cookie if it doesn't exist
   */
  public static function __constructStatic(){
    if(Lobby::$cli)
      return false;
    
    if(!isset($_COOKIE['csrfToken'])){
      self::$token = Helper::randStr(10);
      setcookie("csrfToken", self::$token, 0, "/", Lobby::getHostname());
    }else{
      self::$token = $_COOKIE['csrfToken'];
    }
  }
  
  /**
   * Check if CSRF token matches form data's token
   * @param bool $echo Should error message be printed if it doesn't match
   * @return bool Whether it match
   */
  public static function check($echo = true){
    if(self::$token === Request::get("csrfToken")){
      return true;
    }else{
      if($echo)
        echo ser("Error", "CSRF Token doesn't match. Try again.");
      return false;
    }
  }
  
  /**
   * Get the CSRF token
   * @return string Token
   */
  public static function get(){
    return self::$token;
  }
  
  /**
   * Get as a query paramter
   * @return string "&csrfToken=..."
   */
  public static function getParam(){
    return "&csrfToken=" . self::$token;
  }
  
  /**
   * Get as an <input> field tag
   * @return string HTML of <input> tag
   */
  public static function getInput(){
    return "<input type='hidden' name='csrfToken' value='". self::$token ."' />";
  }

}
