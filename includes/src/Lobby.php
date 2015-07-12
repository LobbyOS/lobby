<?php
/**
 * The Heart of Lobby
 * Other classes extend from this class
 */
class Lobby {

  public static $debug, $root, $host, $cleanHost, $title, $serverCheck, $db, $lid, $error = "";
  public static $installed = false;
  public static $sysinfo, $hooks = array();

  public static $js, $css = array();
  
  public static $valid_hooks = array(
    "init", "body.begin", "admin.body.begin", "head.begin", "router.finish"
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
    
    $docRoot = substr($_SERVER['DOCUMENT_ROOT'], -1) == "/" ? substr_replace($_SERVER['DOCUMENT_ROOT'], "", -1) : $_SERVER['DOCUMENT_ROOT'];
    $host = str_replace($docRoot, $_SERVER['HTTP_HOST'], L_DIR);
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
    
    self::$cleanHost = $host;
    self::$host = $protocol . $host;
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
    
    /* JS */
    if(count(self::$js) != 0){
      /**
       * Load jQuery, jQuery UI, Lobby Main separately without async
       */
      $url = L_URL . "/includes/serve.php?file=" . implode(",", array(self::$js['jquery'], self::$js['jqueryui'], self::$js['main']));
      echo "<script src='{$url}'></script>";
      unset(self::$js['jquery']);
      unset(self::$js['jqueryui']);
      unset(self::$js['main']);
      
      $url = L_URL . "/includes/serve.php?file=" . implode(",", self::$js);
      echo "<script async src='{$url}'></script>";
    }
    /* CSS */
    if(count(self::$css) != 0){
      $url = L_URL . "/includes/serve.php?file=" . implode(",", self::$css);
      if(defined("APP_URL")){
        $url .= "&APP_URL=" . urlencode(APP_URL);
        $url .= "&APP_SRC=" . urlencode(APP_SRC);
      }
      echo "<link async href='{$url}' rel='stylesheet'/>";
    }
    
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
    echo $url;
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
  
  /* Add message to log files */
  public static function log($msg = "", $file = "lobby.log"){
    if( $msg != "" && self::$debug === true ){
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
  
  /* A HTTP Request Function */
  public static function loadURL($url, $params = array(), $type="GET"){
    $ch = curl_init();
    if(count($params) != 0){
      $fields_string = "";
      foreach($params as $key => $value){
      $fields_string .= "{$key}={$value}&";
      }
      /* Remove Last & char */
      rtrim($fields_string, '&');
    }
    
    if($type == "GET" && count($params) != 0){
      /* Append Query String Parameters */
      $url .= "?{$fields_string}";
    }
    
    /* Start Making cURL request */
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if($type == "POST" && count($params) != 0){
      curl_setopt($ch, CURLOPT_POST, count($params));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    }
    /* Give back the response */
    $output = curl_exec($ch);
    return $output;
  }
  
  /* Show Error Messages */
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

  /* Show Success Messages */
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
        $return = $func(\Lobby::curPage());
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
    
    if($path == ""){
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
   */
  public static function curPage($page = false, $full = false){
    $url = self::u("", true);
    $parts = parse_url($url);
    if($page){
      $pathParts = explode("/", $parts['path']);
      $last = $pathParts[ count($pathParts) - 1 ]; // Get the string after last "/"
      return $full === false ? $last : $last . (isset($parts['query']) ? $parts['query'] : "");
    }else{
      return $full === false ? $parts['path'] : $_SERVER["REQUEST_URI"];
    }
  }
}
\Lobby::init();
?>
