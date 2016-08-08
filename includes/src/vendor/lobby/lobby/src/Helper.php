<?php
/**
 * Some helping functions
 * ----------------------
 * A class with various tools that make life easy
 */

class Helper {

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
  
}
