<?php
/**
 * Lobby\Request
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Request.php
 */

use Symfony\Component\HttpFoundation\Request as SymRequest;

/**
 * Handle the HTTP requests
 */
class Request {

  /**
   * The Symfony Request object
   */
  private static $request = null;

  /**
   * The original value of $_SERVER["REQUEST_URI"] before it's altered
   */
  private static $requestURI = null;

  /**
   * Whether request is GET
   */
  private static $isGET = false;

  /**
   * Whether request is POST
   */
  private static $isPOST = false;

  /**
   * Set up class
   */
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
   * @param string $key The key of $_GET or $_POST
   * @param string $default The default value if param doesn't exist in GET or POST data
   * @return string|null Null if the param doesn't exist in both GET and POST data
   */
  public static function get($key, $default = null){
    $val = self::$request->get($key, $default);
    if($val !== null)
      $val = urldecode($val);
    return $val;
  }

  /**
   * Get value from $_GET
   * @param string $key The key of $_GET
   * @param string $default The default value if GET param doesn't exist
   * @return string The GET param or default value
   */
  public static function getParam($key, $default = null){
    return self::$request->query->get($key, $default);
  }

  /**
   * Get POST data
   * @param string $key The key of $_POST
   * @param string $default The default value if POST param doesn't exist
   * @return string The POST param or default value
   */
  public static function postParam($key, $default = null){
    return self::$request->request->get($key, $default);
  }

  /**
   * Get the Symfony Request object used by this class
   * @return Symfony\Component\HttpFoundation\Request Symfony's Request Object
   */
  public static function getRequestObject(){
    return self::$request;
  }

  /**
   * Get the original $_SERVER["REQUEST_URI"]
   * @return string The value that was originally in $_SERVER["REQUEST_URI"] before it was altered
   */
  public static function getRequestURI(){
    return self::$requestURI;
  }


  /**
   * Check if it is a GET request
   * @return bool Whether the request has GET data
   */
  public static function isGET(){
    return self::$isGET;
  }

  /**
   * Check if it is a POST request
   * @return bool Whether the request has POST data
   */
  public static function isPOST(){
    return self::$isPOST;
  }

}
