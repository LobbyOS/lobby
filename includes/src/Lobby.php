<?php
/**
 * The Heart of Lobby
 * Other classes extend from this class
 */
class Lobby {

  public static $debug, $root, $url, $host_name, $title, $serverCheck, $db, $lid, $error = "";
  public static $installed = false;
  public static $sysinfo, $hooks = array();

  public static $js, $css = array();
  
  public static $valid_hooks = array(
    "init", "body.begin", "admin.body.begin", "head.begin", "admin.head.begin", "head.end", "router.finish"
  );
  public static $config = array(
    "db" => array(),
    "debug" => false,
    "server_check" => true
  );
  
  public static $statuses = array();
  private static $status = null;
 
  public static function init(){
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
      self::$host_name = $url_parts['host'];
      self::$url = self::$config['lobby_url'];
    }else{
      $base_dir  = L_DIR;
      $doc_root  = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']);
      $base_url  = preg_replace("!^${doc_root}!", '', $base_dir); # ex: '' or '/mywebsite'
      $protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
      $port      = $_SERVER['SERVER_PORT'];
      $disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
      $domain    = $_SERVER['SERVER_NAME'];
      
      self::$url  = "${protocol}://${domain}${disp_port}${base_url}";
      self::$host_name = $domain;
    }
  }
  
  /**
   * Reads configuration & set Lobby according to it
   */
  public static function config($db = false){
    if(file_exists(L_DIR . "/config.php")){
      $config = include(L_DIR . "/config.php");
      
      if(is_array($config) && count($config) != 0){
        self::$config = array_merge(self::$config, $config);
        
        $config = self::$config;
        if($db === true){
          return $config['db'];
        }else{
          if($config['debug'] === true){
            ini_set("display_errors","on");
            self::$debug = true;
          }
          self::$lid = $config['lobbyID'];// The Global Lobby installation ID
        }
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
 
  public static function addScript($name, $url){
    self::$js[$name] = $url;
  }
 
  public static function addStyle($name, $url){
    self::$css[$name] = $url;
  }
 
  public static function head($title = ""){
    if($title != ""){
      self::setTitle($title);
    }
    
    /**
     * JS Files
     */
    if(count(self::$js) != 0 && !\Lobby::status("lobby.install")){
      /**
       * Load jQuery, jQuery UI, Lobby Main, App separately without async
       */
      $url = L_URL . "/includes/serve.php?file=" . implode(",", array(self::$js['jquery'], self::$js['jqueryui'], self::$js['main'], isset(self::$js['app']) ? self::$js['app'] : ""));
      echo "<script src='{$url}'></script>";
      unset(self::$js['jquery']);
      unset(self::$js['jqueryui']);
      unset(self::$js['main']);
      
      $url = L_URL . "/includes/serve.php?file=" . implode(",", self::$js);
      echo "<script>lobby.load_script_url = '{$url}';</script>";
    }
    /**
     * CSS Files
     */
    if(count(self::$css) != 0){
      $url = L_URL . "/includes/serve.php?file=" . implode(",", self::$css);
      if(defined("APP_URL")){
        $url .= "&APP_URL=" . urlencode(APP_URL);
        $url .= "&APP_SRC=" . urlencode(APP_SRC);
      }
      echo "<link async href='{$url}' rel='stylesheet'/>";
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
   * Add message to log files 
   */
  public static function log($msg = "", $file = "lobby.log"){
    $msg = !is_string($msg) ? serialize($msg) : $msg;
    if($msg != "" && self::$debug === true){
      $logFile = "/contents/extra/{$file}";
      $message = "[" . date("Y-m-d H:i:s") . "] $msg";
      \Lobby\FS::write($logFile, $message, "a");
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
      include(L_DIR . "/includes/lib/core/Inc/error.php");
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
    self::$sysinfo = $info;
  }
  
  /* Hooks */
  public static function hook($place, $function){
    if(array_search($place, self::$valid_hooks) !== false){
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
        }elseif($path == "/includes/serve.php"){
          $status = "lobby.serve";
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
   * Make a URL from Lobby Base. Eg: /hello to http://lobby.dev/lobby/hello
   */
  public static function u($path = "", $relative = false){
    $orPath = $path; // The original path
    $path = substr($path, 0, 1) == "/" ? substr($path, 1) : $path;
    $url = $path;
    $parts = parse_url($path);
    
    if($path === ""){
      /**
       * If no path, give the current page URL
       */
      $pageURL = 'http';
      if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
      }
      
      $pageURL .= "://";
      $request_uri = $relative === false ? $_SERVER["ORIG_REQUEST_URI"] : $_SERVER["REQUEST_URI"];
      
      if($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $request_uri;
      }else{
        $pageURL .= $_SERVER["SERVER_NAME"] . $request_uri;
      }
      
      $url = $pageURL;
    }elseif($path === L_URL){
      $url = L_URL;
    }elseif(!preg_match("/http/", $path) || $parts['host'] != $_SERVER['HTTP_HOST']){
      if(!defined("APP_DIR") || substr($orPath, 0, 1) == "/"){
        $url = L_URL . "/$path";
      }else{
        $url = \Lobby\App::u($orPath);
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
    $url = self::u("", true);
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
\Lobby::init();
?>
