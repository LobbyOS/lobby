<?php
/**
 * Some helping functions
 * ----------------------
 * A class with various tools that make life easy
 */

class H {
  
  public static function init(){
    if(!isset($_COOKIE['csrf_token'])){
      $token = self::randStr(10);
      setcookie("csrf_token", $token, 0);
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
  public static function input($name, $type = ""){
    $post_count = count($_POST);
    $get_count = count($_GET);
    
    if($post_count !== 0 && $get_count !== 0){
      /**
       * Both $_GET and $_POST are present
       */
      $arr = $_GET + $_POST;
    }else{
      if($type === "GET" || ($type !== "POST" && $get_count !== 0 && $post_count === 0)){
        $arr = $_GET;
      }else if($type == "POST" || $post_count != 0){
        $arr = $_POST;
      }
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
  
  /**
   * Get JSON decoded array from a value of App's Data Storage
   */
  public static function getJSONData($key){
    $a = getData($key);
    $a = json_decode($a, true);
    return is_array($a) ? $a : array();
  }
  
  /**
   * Save JSON as a value of App's Data Storage
   */
  public static function saveJSONData($key, $values){
    $a = getData($key);
    $a = json_decode($a, true);
    $a = is_array($a) ? $a : array();
    
    $new = array_merge_recursive($a, $values);    
    foreach($values as $k => $v){
      if($v === false){
        unset($new[$k]);
      }
    }
    $new = json_encode($new);
    saveData($key, $new);
    return true;
  }
}
H::init();
