<?php
/**
 * Some helping functions
 * ----------------------
 * A class with various tools that make life easy
 */

class H {
  
  public static function init(){
    if(!isset($_COOKIE['csrf_token'])){
      $token = base64_encode(rand(20000, 30000));
      setcookie("csrf_token", $token, 0);
      $_COOKIE['csrf_token'] = $token;
    }
  }
  
  /**
   * Get value from $_GET and $_POST
   * returns null if it doesn't exist
   */
  public static function input($name, $type = ""){
    if($type == "GET" || (count($_GET) != 0 && $type != "POST")){
      $arr = $_GET;
    }elseif($type == "POST" || count($_POST) != 0){
      $arr = $_POST;
    }
    if(isset($arr[$name])){
      return urldecode($arr[$name]);
    }else{
      return null;
    }
  }
  
  /**
   * CSRF token check
   */
  public static function csrf($type = false){
    if($type == "s"){
      // Output as string
      return urlencode($_COOKIE['csrf_token']);
    }elseif($type == "g"){
      // Output as a GET parameter
      return "&csrf_token=" . urlencode($_COOKIE['csrf_token']);
    }elseif($type !== false){
      // Output as an input field
      echo "<input type='hidden' name='csrf_token' value='{$_COOKIE['csrf_token']}' />";
    }else{
      // Check CSRF validity
      if($_COOKIE['csrf_token'] == self::input('csrf_token')){
        return true;
      }else{
        ser("Error", "CSRF Token doesn't match. Try again.");
        return false;
      }
    }
  }
}
H::init();
