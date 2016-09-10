<?php
/**
 * The Heart of Lobby
 */

use Lobby\Apps;
use Lobby\CLI;
use Lobby\FS;

/**
 * The main class that sets up everything
 */
class Lobby {

  /**
   * Version & Release date
   */
  public static $version, $versionName, $versionReleased;

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

  /**
   * Whether Lobby is installed
   */
  public static $installed = false;

  /**
   * Information about system Lobby is installed in
   */
  protected static $sysInfo = array();

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
   * Array that hold the functions to determine state of Lobby
   */
  public static $statuses = array();

  /**
   * Store the current state of Lobby
   */
  private static $status = null;

  /**
   * Whether Lobby is in CLI (Command Line Interface) mode
   */
  public static $cli = false;

  /**
   * Set up
   */
  public static function __constructStatic(){
    /**
     * Callback on fatal errors
     */
    register_shutdown_function(function(){
      return \Lobby::fatalErrorHandler();
    });

    set_error_handler(function(){
      return \Lobby::fatalErrorHandler();
    });

    if(!isset($_SERVER["SERVER_NAME"])){
      /**
       * Lobby is not loaded by browser request, but by a script
       * so $_SERVER vars won't exist. This will cause problems
       * for URL making, hence we must define it's CLI
       */
      self::$cli = true;
    }else{
      session_start();
    }

    self::sysInfo();
    self::config();

    $lobbyInfo = FS::get("/lobby.json");
    if($lobbyInfo !== false){
      $lobbyInfo = json_decode($lobbyInfo);
      self::$version = $lobbyInfo->version;
      self::$versionName = $lobbyInfo->codename;
      self::$versionReleased = $lobbyInfo->released;
    }

    \Assets::config(array(
      "basePath" => L_DIR,
      "baseURL" => self::getURL(),
      "serveFile" => "includes/serve-assets.php",
      "debug" => self::getConfig("debug")
    ));
  }

  /**
   * Reads configuration & set Lobby according to it
   * @param bool $db Whether database config should only be returned
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

  /**
   * Get the config value of a key
   * @param string $key The config key
   * @param string $subKey The sub config key
   * @return mixed The config value
   */
  public static function getConfig($key, $subKey = null){
    if(isset(self::$config[$key])){
      if($subKey === null)
        return self::$config[$key];
      else
        return isset(self::$config[$key][$subKey]) ? self::$config[$key][$subKey] : false;
    }else{
      return false;
    }
  }

  /**
   * Return the version string of Lobby
   * @param bool $codename Whether release name (Berly) should also be included
   * @return string Lobby version
   */
  public static function getVersion($codename = false){
    return self::$version . ($codename ? " " . self::$versionName : "");
  }

  /**
   * Log messages to a log file
   * @param string $msg The message to log
   * @param string $file Log file path relative to 'contents/extra'
   * @return bool Returns false if debugging is disabled
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

    if(self::$cli)
      return null;

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

  /**
   * Get the host with the port (127.0.0.1:2020, localhost).
   * If the port is '80', it won't be included in the return value
   * @return string Host where Lobby is running
   */
  public static function getHost(){
    return self::$host;
  }

  /**
   * Get the hostname (without the port)
   * @return string Hostname
   */
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

      if(!self::$cli)
        Response::showError("Fatal Error", $error);
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

  /**
   * Get info about the system Lobby is running in
   * @param string $key
   * @return string The config value
   */
  public static function getSysInfo($key = null){
    return self::$sysInfo[$key];
  }

  /**
   * Check the status of Lobby
   *
   * @param string $val The status to match against
   *
   * @return bool Whether $val matches the current Lobby status
   */
  public static function status($val){
    $status = "";
    if(self::$status !== null){
      $status = self::$status;
    }else{
      self::$statuses[] = function($path){
        if($path === "/admin/install.php"){
          $status = "lobby.install";
        }else if(substr($path, 0, 6) === "/admin"){
          $status = "lobby.admin";
        }else if($path === "/includes/serve-assets.php"){
          $status = "lobby.assets-serve";
        }
        return isset($status) ? $status : false;
      };
      foreach(self::$statuses as $func){
        $return = $func(self::curPage());
        if($return !== false){
          $status = $return;
        }
      }
      self::$status = $status;
    }
    return $status === $val;
  }

  /**
   * ----------------
   * Helper functions
   * ----------------
   */

  /**
   * Make a hyperlink
   *
   * @param string $url   The URL
   * @param string $text  Inner content of hyperlink
   * @param string $extra Extra code to be inserted before <a is closed
   *
   * @return string The hyperlink code
   */
  public static function l($url = null, $text = null, $extra = null) {
    $url = self::u($url);
    return '<a href="'. $url .'" '. $extra .'>'. $text .'</a>';
  }

  /**
   * Make a Lobby URL from relative path
   *
   * @param string $path The relative path
   * @param string $relative
   *
   * @return Type Description
   */
  public static function u($path = null, $relative = false){
    if(self::$cli)
      return null;

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
        $urlHost = null;
      }
    }

    /**
     * If no path, give the current page URL
     */
    if($path == null){
      $requestURI = $relative === false ? Request::getRequestURI() : $_SERVER["REQUEST_URI"];
      $url .= self::getURL() . $requestURI;
    }else if($path === self::$url){
      $url = self::$url;
    }else if(!preg_match("/http/", $path) || $urlHost !== self::$host){
      /**
       * If $origPath is a relative URI
       */
      if($urlHost == null){
        $url = self::$url . "/$path";
      }else if(Apps::isAppRunning()){
        $url = Apps::getRunningInstance()->u($origPath);
      }
    }
    return $url;
  }

  /**
   * Get the path of current URL.
   * To get the last part only ("install" in "/folder/subfolder/admin/install), pass TRUE to $page
   *
   * @param bool $page Whether only the last pagename of URL should be returned.
   * @param bool $full Whether query part of URL should be included
   * @return string Pathname
   */
  public static function curPage($page = false, $full = false){
    $parts = parse_url($_SERVER["REQUEST_URI"]);

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
