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
      $_COOKIE,
      $_FILES,
      $_SERVER
    );
  }
  
  public static function get($key){
    return $request->query->get($key);
  }
  
  public static function getRequestObject(){
    return self::$request;
  }

}
