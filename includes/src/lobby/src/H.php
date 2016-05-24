<?php
/**
 * Some helping functions
 * ----------------------
 * A class with various tools that make life easy
 */

class H {
  
  public static function __constructStatic(){
    if(!isset($_COOKIE['csrf_token'])){
      $token = self::randStr(10);
      setcookie("csrf_token", $token, 0, "/", \Lobby::$hostName);
      $_COOKIE['csrf_token'] = $token;
    }
  }
  
  /**
   * Generate a random string
   */
  public static function randStr($length){
    $str = "";
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $size = 62; // strlen($chars)
    for($i=0;$i < $length;$i++){
      $str .= $chars[rand(0, $size-1)];
    }
    return $str;
  }
  
  /**
   * Get value from $_GET and $_POST according to request
   * returns null if it doesn't exist
   */
  public static function i($name, $default_val = null, $type = null){
    return input($name, $default_val, $type);
  }
  
  /**
   * CSRF token check
   * ----------------
   * 's' for outputting as string
   * 'g' for a GET param URI
   * (bool) FALSE for checking CSRF token present with request
   * Any other vals will output an input field containing token
   */
  public static function csrf($type = false){
    if($type === "s"){
      // Output as string
      return urlencode($_COOKIE['csrf_token']);
    }else if($type === "g"){
      // Output as a GET parameter
      return "&csrf_token=" . urlencode($_COOKIE['csrf_token']);
    }else if($type !== false){
      // Output as an input field
      echo "<input type='hidden' name='csrf_token' value='{$_COOKIE['csrf_token']}' />";
    }else{
      // Check CSRF validity
      if($_COOKIE['csrf_token'] == self::i('csrf_token')){
        return true;
      }else{
        echo ser("Error", "CSRF Token doesn't match. Try again.");
        return false;
      }
    }
  }
  
  /**
   * Get JSON decoded array from a value of App's Data Storage
   */
  public static function getJSONData($key, $appID = null){
    $a = getData($key, false, $appID);
    $a = json_decode($a, true);
    return is_array($a) ? $a : array();
  }
  
  /**
   * Save JSON as a value of App's Data Storage
   * To remove an item, set the value of it to (bool) FALSE
   */
  public static function saveJSONData($key, $values, $appID = null){
    $a = self::getJSONData($key, $appID);
    
    $new = array_replace_recursive($a, $values);
    foreach($values as $k => $v){
      if($v === false){
        unset($new[$k]);
      }
    }
    $new = json_encode($new);
    saveData($key, $new, $appID);
    return true;
  }
}
