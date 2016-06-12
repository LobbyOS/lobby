<?php
/**
 * SymRequest = Symfony Request
 */
use Symfony\Component\HttpFoundation\Request as SymRequest;

class Request {

  private static $request = null;

  public static function __constructStatic(){
    self::$request = new SymRequest(
      $_GET,
      $_POST,
      array(),
      array(),
      $_FILES,
      $_SERVER
    );
  }
  
  /**
   * Get value from $_GET & $_POST
   * returns null if it doesn't exist
   * @param $name string - The key
   * @param $default string - The default value that should be returned
   * @param $type string - Explicitly mention where to get value from ("GET" or "POST")
   */
  public static function get($key, $default = null){
    return self::$request->get($key, $default);
  }
  
  public static function getParam($key, $default = null){
    return self::$request->query->get($key, $default);
  }
  
  public static function postParam($key, $default = null){
    return self::$request->request->get($key, $default);
  }
  
  public static function getRequestObject(){
    return self::$request;
  }

}
