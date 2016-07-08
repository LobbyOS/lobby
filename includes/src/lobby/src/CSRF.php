<?php
/**
 * Protect from CSRF attacks
 */

class CSRF {

  protected static $token = null;

  public static function __constructStatic(){
    if(!isset($_COOKIE['csrfToken'])){
      self::$token = Helper::randStr(10);
      setcookie("csrfToken", self::$token, 0, "/", Lobby::getHostname());
    }else{
      self::$token = $_COOKIE['csrfToken'];
    }
  }
  
  /**
   * Check if CSRF token matches
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
   * Get the token
   */
  public static function get(){
    return self::$token;
  }
  
  /**
   * Get as a paramter "&csrfToken="
   */
  public static function getParam(){
    return "&csrfToken=" . self::$token;
  }
  
  public static function getInput(){
    return "<input type='hidden' name='csrfToken' value='". self::$token ."' />";
  }

}
