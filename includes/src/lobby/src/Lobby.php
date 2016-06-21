<?php
/**
 * The Heart of Lobby
 */
 
use Lobby\Apps;
 
class Lobby {

  /**
   * Version & Release date
   */
  public static $version, $versionReleased;
  
  /**
   * Debugging Mode
   */
  public static $debug = false;
  
  /**
   * Base URL
   */
  protected static $url = null;
  
  /**
   * Host
   * 127.0.0.1:8000, localhost:9000
   */
  protected static $host;
  
  /**
   * hostname = Host without port
   * 127.0.0.1, localhost
   */
  protected static $hostname;
  
  /**
   * The Lobby Public ID
   */
  protected static $lid;
  
  public static $installed = false;
  
  /**
   * Hooks available in Lobby
   */
  protected static $validHooks = array(
    "init", "body.begin", "admin.body.begin", "head.begin", "admin.head.begin", "head.end", "router.finish", "panel.end"
  );
  
  protected static $sysInfo, $hooks = array();
  
  /**
   * Default Config
   */
  private static $config = array(
    "db" => array(
      "type" => "mysql"
    ),
    "debug" => false,
    "server_check" => true
  );
  
  /**
   * $statues Array that hold the functions to determine status
   * $status Property that stores the current state of Lobby
   */
  public static $statuses = array();
  private static $status = null;
  
  /**
   * The base directory of Lobby
   */
  private static $lDir = null;
 
  public static function __constructStatic(){
    /**
     * Callback on fatal errors
     */
    register_shutdown_function(function(){
      return \Lobby::fatalErrorHandler();
    });
    
    self::sysInfo();
    self::config();
    
    \Assets::config(array(
      "basePath" => L_DIR,
      "baseURL" => self::getURL(),
      "serveFile" => "includes/serve-assets.php",
      "debug" => self::getConfig("debug")
    ));
  }
  
  /**
   * Reads configuration & set Lobby according to it
   */
  public static function config($db = false){
    if(file_exists(L_DIR . "/config.php")){
      $config = include(L_DIR . "/config.php");
      
      if(is_array($config) && count($config) != 0){
        self::$config = array_replace_recursive(self::$config, $config);

        if($db === true){
          return self::$config['db'];
        }else{
          if(self::$config['debug'] === true){
            ini_set("display_errors","on");
            self::$debug = true;
          }
          self::$lid = self::$config['lobbyID']; // The Global Lobby installation ID
        }
      }else{
        return false;
      }
      
    }else{
      return false;
    }
  }
  
  public static function getConfig($key){
    return isset(self::$config[$key]) ? self::$config[$key] : false;
  }
  
  /**
   * Add message to log files 
   */
  public static function log($msg = null, $file = "lobby.log"){
    /**
     * If $msg is an array, it means the errors are SERIOUS
     * Array(
     *   0 => "Type of Error",
     *   1 => "Message"
     * )
     */
    if(is_array($msg)){
      $type = $msg[0];
      $logMSG = ucfirst($type) . " Error - " . $msg[1];
    }else if(self::$debug === false)
      return false;
    
    if($msg != null){
      $logMSG = !is_string($msg) ? serialize($msg) : $msg;
    }
    
    /**
     * Write to Log File
     */
    if($msg != null){
      $logFile = "/contents/extra/logs/{$file}";
      
      /**
       * Format the log message 
       */
      $logMSG = "[" . date("Y-m-d H:i:s") . "] $logMSG";
      \Lobby\FS::write($logFile, $logMSG, "a");
    }
    
    /**
     * If error is Fatal, Lobby can't work
     * So register error in class
     */
    if(isset($type) && $type === "fatal"){
      Response::showError(ucfirst($msg[0]) . " Error", $msg[1]);
    }
  }
  
  /**
   * Get/Make the Lobby base URL
   */
  public static function getURL(){
    if(self::$url !== null)
      return self::$url;
      
    if(isset(self::$config['lobby_url'])){
      $url_parts = parse_url(self::$config['lobby_url']);
      self::$hostname = $url_parts['host'];
      self::$url = self::$config['lobby_url'];
    }else{
      $docDir = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
      $subdir = str_replace($docDir, '', L_DIR);
      $urladdr = $_SERVER['HTTP_HOST'] . $subdir;
      $urladdr = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $urladdr;
      
      self::$url = rtrim($urladdr, "/");
      self::$host = $_SERVER['SERVER_NAME'] . (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ? ":{$_SERVER['SERVER_PORT']}" : "");
      self::$hostname = $_SERVER['SERVER_NAME'];
    }
    return self::$url;
  }
  
  public static function getHost(){
    return self::$host;
  }
  
  public static function getHostname(){
    return self::$hostname;
  }
  
  /**
   * A handler for Fatal Errors occured in PHP
   */
  public static function fatalErrorHandler(){
    $error = error_get_last();

    if( $error !== NULL) {
      $errType = $error["type"];
      $errFile = $error["file"];
      $errLine = $error["line"];
      $errStr = $error["message"];
      
      $error = "$errType caused by $errFile on line $errLine : $errStr";
      self::log($error);
    }
  }
  
  /**
   * Load System Info into self::$sysInfo
   */
  private static function sysInfo(){
    $info = array();

    /* Get the OS */
    $os = strtolower(substr(php_uname('s'), 0, 3));
    if ($os == 'lin') {
      $info['os'] = "linux";
    }else if ($os == 'win') {
      $info['os'] = "windows";
    }else if ($os == 'mac') {
      $info['os'] = "mac";
    }
    self::$sysInfo = $info;
  }
  
  public static function getSysInfo($key = null){
    return self::$sysInfo[$key];
  }
  
  /**
   * Hooks
   */
  public static function hook($place, $function){
    /**
     * Multiple hooks with same $function
     */
    if(preg_match("/\,/i", $place) !== 0){
      $output = array();
      $places = explode(",", $place);
      foreach($places as $place){
        $output[] = self::hook($place, $function);
      }
      return $output;
    }else if(array_search($place, self::$validHooks) !== false){
      self::$hooks[$place][] = $function;
      return true;
    }else{
      return false;
    }
  }
  
  public static function doHook($place){
    if(isset(self::$hooks[$place])){
      foreach(self::$hooks[$place] as $hook){
        $hook();
      }
    }
  }
  
  /**
   * Get status
   */
  public static function status($val){
    $status = "";
    if(self::$status != null){
      $status = self::$status;
    }else{
      self::$statuses[] = function($path){
        if($path == "/admin/install.php"){
          $status = "lobby.install";
        }elseif(substr($path, 0, 6) == "/admin"){
          $status = "lobby.admin";
        }elseif($path == "/includes/serve-assets.php"){
          $status = "lobby.assets-serve";
        }
        return isset($status) ? $status : false;
      };
      foreach(self::$statuses as $func){
        $return = $func(self::curPage());
        if($return != false){
          $status = $return;
        }
      }
      self::$status = $status;
    }
    return $status == $val;
  }

  /**
   * ----------------
   * Helper functions
   * ----------------
   */

  /**
   * Make a hyperlink
   */
  public static function l($url = "", $text = "", $extra = "") {
    $url = self::u($url);
    return '<a href="'. $url .'" '. $extra .'>'. $text .'</a>';
  }

  /**
   * Make a URL from Lobby Base Path.
   * Eg: /hello to http://lobby.dev/hello
   */
  public static function u($path = null, $relative = false){
    /**
     * The $path var is changed during the process
     * So, original path is stored separately
     */
    $origPath = $path;
    
    /**
     * The return URL
     */
    $url = $path;
    
    /**
     * Prettyify $path
     */
    if($path !== null){
      $path = ltrim($path, "/");
      
      $parts = parse_url($path);
      
      /**
       * Make host along with port:
       * 127.0.0.1:9000
       */
      if(isset($parts['host'])){
        $urlHost = $parts['host'] . (isset($parts['port']) ? ":{$parts['port']}" : "");
      }else{
        $urlHost = "";
      }
    }
    
    /**
     * If no path, give the current page URL
     */
    if($path == null){
      $pageURL = 'http';
      if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
      }
      
      $pageURL .= "://";
      $request_uri = $relative === false ? $_SERVER["ORIG_REQUEST_URI"] : $_SERVER["REQUEST_URI"];
      
      if(isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $request_uri;
      }else{
        $pageURL .= $_SERVER["SERVER_NAME"] . $request_uri;
      }
      
      $url = $pageURL;
    }else if($path === self::$url){
      $url = self::$url;
    }else if(!preg_match("/http/", $path) || $urlHost !== self::$host){
      /**
       * If $origPath is a relative URI
       */
      if(Apps::isAppRunning() || $urlHost == null){
        $url = self::$url . "/$path";
      }else{
        $url = \Lobby\App::u($origPath);
      }
    }
    return $url;
  }
  
  /**
   * Get the current page
   * --------------------
   * To get the query part of the URL too, pass TRUE to $full
   * To get the last part only ("install" in "/folder/subfolder/admin/install), pass TRUE to $page
   */
  public static function curPage($page = false, $full = false){
    $url = self::u(null, true);
    $parts = parse_url($url);
    
    if($page){
      $pathParts = explode("/", $parts['path']);
      /**
       * Get the string after last "/"
       */
      $last = $pathParts[ count($pathParts) - 1 ];
      
      return $full === false ? $last : $last . (isset($parts['query']) ? $parts['query'] : "");
    }else{
      return $full === false ? $parts['path'] : $_SERVER["REQUEST_URI"];
    }
  }
  
  /**
   * Get the public Lobby ID
   */
  public static function getLID(){
    return self::$lid;
  }
  
}
