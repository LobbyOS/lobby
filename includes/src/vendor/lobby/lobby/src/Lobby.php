<?php
/**
 * The Heart of Lobby
 * Other classes extend from this class
 */
 
class Lobby {

  public static $version, $versionReleased, $debug, $root, $url, $host, $hostName, $title, $serverCheck, $db, $lid, $error = null;
  
  public static $installed = false;
  
  public static $sysInfo, $hooks = array();
  
  public static $valid_hooks = array(
    "init", "body.begin", "admin.body.begin", "head.begin", "admin.head.begin", "head.end", "router.finish",
    "panel.end"
  );
  public static $config = array(
    "db" => array(
      "type" => "mysql"
    ),
    "debug" => false,
    "server_check" => true
  );
  
  public static $statuses = array();
  
  private static $status = null;
 
  public static function __constructStatic(){
    
    /**
     * Callback on fatal errors
     */
    register_shutdown_function(function(){
      return \Lobby::fatalErrorHandler();
    });
    
    self::sysinfo();
    self::config();
    
    if(isset(self::$config['lobby_url'])){
      $url_parts = parse_url(self::$config['lobby_url']);
      self::$hostName = $url_parts['host'];
      self::$url = self::$config['lobby_url'];
    }else{
      $docDir = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
      $subdir = str_replace($docDir, '', L_DIR);
      $urladdr = $_SERVER['HTTP_HOST'] . $subdir;
      $urladdr = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $urladdr;
      
      self::$url = rtrim($urladdr, "/"); // Remove Trailing Slash
      /**
       * Host with Port
       */
      self::$host = $_SERVER['SERVER_NAME'] . (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ? ":{$_SERVER['SERVER_PORT']}" : "");
      self::$hostName = $_SERVER['SERVER_NAME'];
    }
    
    \Assets::config(array(
      "basePath" => L_DIR,
      "baseURL" => self::$url,
      "serveFile" => "includes/serve-assets.php"
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
 
  public static function head($title = ""){
    header('Content-type: text/html; charset=utf-8');
    if($title != ""){
      self::setTitle($title);
    }
    
    if(isset(Assets::$js['jquery'])){
      /**
       * Load jQuery, jQuery UI, Lobby Main, App separately without async
       */
      $url = L_URL . "/includes/serve-assets.php?type=js&assets=" . implode(",", array(
        Assets::$js['jquery'],
        Assets::$js['jqueryui'],
        Assets::$js['main'],
        isset(Assets::$js['app']) ? Assets::$js['app'] : ""
      ));
      echo "<script src='{$url}'></script>";
      
      Assets::removeJs("jquery");
      Assets::removeJs("jqueryui");
      Assets::removeJs("main");
    }
    
    echo "<script>lobby.load_script_url = '". Assets::getServeURL("js") ."';</script>";
    
    /**
     * CSS Files
     */
    if(defined("APP_URL")){
      echo Assets::getServeLinkTag(array(
        "APP_URL" => urlencode(APP_URL),
        "APP_SRC" => urlencode(APP_SRC)
      ));
    }else{
      echo Assets::getServeLinkTag();
    }
    
    echo "<link href='". L_URL ."/favicon.ico' sizes='16x16 32x32 64x64' rel='shortcut icon' />";
    
    /* Title */
    echo "<title>" . self::$title . "</title>";
  }
 
  /* Set the Page title */
  public static function setTitle($title = ""){
    if($title != ""){
      self::$title = $title;
      if(self::$title == ""){
        self::$title = "Lobby";
      }else{
        self::$title .= " - Lobby";
      }
    }
  }
  
  /* A redirect function that support HTTP status code for redirection 
   * 302 - Moved Temporarily
  */
  public static function redirect($url, $status = 302){
    $url = self::u($url);
    header("Location: $url", true, $status);
    exit;
    return true;
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
      $msg = ucfirst($type) . " Error - " . $msg[1];
      
      /**
       * If error is Fatal, Lobby can't work
       * So register error in class
       */
      if($type === "fatal"){
        self::$error = $msg;
      }
    }else if($msg != "" && self::$debug === true){
      $msg = !is_string($msg) ? serialize($msg) : $msg;
    }
    
    /**
     * Write to Log File
     */
    if($msg != null){
      $logFile = "/contents/extra/logs/{$file}";
      
      /**
       * Format the log message 
       */
      $msg = "[" . date("Y-m-d H:i:s") . "] $msg";
      \Lobby\FS::write($logFile, $msg, "a");
    }
  }
  
  /* A handler for Fatal Errors occured in PHP */
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
   * A HTTP Request Function
   */
  public static function loadURL($url, $params = array(), $type="GET"){
    $ch = curl_init();
    
    $fields_string = "";
    if(count($params) != 0){
      /**
       * Add Lobby ID
       */
      $params["lobbyID"] = self::$lid;
      
      foreach($params as $key => $value){
        $fields_string .= "{$key}={$value}&";
      }
      /**
       * Remove Last & char
       */
      rtrim($fields_string, '&');
    }
    
    if($type == "GET" && count($params) != 0){
      /* Append Query String Parameters */
      $url .= "?{$fields_string}";
    }
    
    /**
     * Set options of cURL request
     */
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/ca_bundle.crt");
    
    if($type == "POST"){
      curl_setopt($ch, CURLOPT_POST, count($params));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    }
    
    /**
     * Give back the response
     */
    $output = curl_exec($ch);
    return $output;
  }
  
  /**
   * Show Error Messages
   */
  public static function ser($title = "", $description = "", $exit = false){
    $html = "";
    if($title == ''){
      /**
       * If no Title, give a 404 Page
       */
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
      include(L_DIR . "/includes/lib/lobby/inc/error.php");
      exit;
    }else{
      $html .= "<div class='message'>";
        $html .= "<div style='color:red;' class='title'>$title</div>";
        if($description != ""){
          $html .= "<div style='color:red;'>$description</div>";
        }
      $html .= "</div>";
    }
    echo $html;
    if($exit){
      exit;
    }
  }

  /**
   * Show Success Messages
   */
  public static function sss($title, $description){
    $html = "<div class='message'>";
    if($title == ""){
      $html .= "<div style='color:green;' class='title'>Success</div>";
    }else{
      $html .= "<div style='color:green;' class='title'>$title</div>";
    }
    if($description != ""){
      $html .= "<div style='color:green;'>$description</div>";
    }
    $html .= "</div>";
    echo $html;
  }
  
  /**
   * Show Neutral Messages
   */
  public static function sme($title, $description = ""){
    $html = "<div class='message'>";
    if($title == ""){
      $html .= "<div style='color:black;' class='title'>Message</div>";
    }else{
      $html .= "<div style='color:black;' class='title'>$title</div>";
    }
    if($description != ""){
      $html .= "<div style='color:black;'>$description</div>";
    }
    $html .= "</div>";
    echo $html;
  }
  
  /* Identify System Info */
  private static function sysinfo(){
    self::$root = L_DIR;
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
    }else if(array_search($place, self::$valid_hooks) !== false){
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
    }else if($path === L_URL){
      $url = L_URL;
    }else if(!preg_match("/http/", $path) || $urlHost !== self::$host){
      /**
       * If $origPath is a relative URI
       */
      if(!defined("APP_DIR") || substr($origPath, 0, 1) === "/"){
        $url = L_URL . "/$path";
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
  
}
