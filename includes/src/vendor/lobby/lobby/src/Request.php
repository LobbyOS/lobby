<?php
/**
 * SymRequest = Symfony Request
 */
use Symfony\Component\HttpFoundation\Request as SymRequest;

class Request {

  private static $request = null;
  private static $requestURI = null;
  
  /**
   * Whether request is GET or POST
   */
  private static $isGET = false;
  private static $isPOST = false;

  public static function __constructStatic(){
    if(!Lobby::$cli){
      self::$requestURI = $_SERVER['REQUEST_URI'];
      
      /**
       * Make the request URL relative to the base URL of Lobby installation.
       * http://localhost/lobby will be changed to "/"
       * and http://lobby.local to "/"
       * ---------------------
       * We do this directly to $_SERVER['REQUEST_URI'] because, Klein (router)
       * obtains the value from it. Hence we keep the original value in self::$requestURI
       */
      $lobbyBase = str_replace(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']), "", L_DIR);
      $lobbyBase = substr($lobbyBase, 0) == "/" ? substr_replace($lobbyBase, "", 0) : $lobbyBase;
      
      $_SERVER['REQUEST_URI'] = str_replace($lobbyBase, "", $_SERVER['REQUEST_URI']);
      $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], -1) == "/" && $_SERVER['REQUEST_URI'] != "/" ? substr_replace($_SERVER['REQUEST_URI'], "", -1) : $_SERVER['REQUEST_URI'];
    }
    
    self::$request = new SymRequest(
      $_GET,
      $_POST,
      array(),
      array(),
      $_FILES,
      $_SERVER
    );
    
    self::$isGET = !empty($_GET);
    self::$isPOST = !empty($_POST);
  }
  
  /**
   * Get value from $_GET & $_POST
   * returns null if it doesn't exist
   * @param $name string - The key
   * @param $default string - The default value that should be returned
   * @param $type string - Explicitly mention where to get value from ("GET" or "POST")
   */
  public static function get($key, $default = null){
    $val = self::$request->get($key, $default);
    if($val !== null)
      $val = urldecode($val);
    return $val;
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
  
  public static function getRequestURI(){
    return self::$requestURI;
  }
  
  public static function isGET(){
    return self::$isGET;
  }
  
  public static function isPOST(){
    return self::$isPOST;
  }

}
