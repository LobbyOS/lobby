<?php
/**
 * Francium Assets Manager
 * For CSS, JS
 */
 
class Assets {

  protected static $css, $js = array();
  
  protected static $config = array(
    /**
     * Base Directory (path) of application
     */
    "basePath" => "",
    
    /**
     * The base URL of all packages
     */
    "baseURL" => "",
    
    /**
     * The path to file where assets are printed
     */
    "serveFile" => "",
    
    /**
     * Enable/Disable Debugging
     */
    "debug" => false
  );
  
  /**
   * Callback before asset contents are served
   */
  public static $preProcess = null;

  public static function config($config){
    self::$config = array_replace_recursive(self::$config, $config);
    self::$config["basePath"] = realpath(self::$config["basePath"]);
  }
  
  /**
   * Make URL from path
   */
  public static function getURL($path){
    if(filter_var($path, FILTER_VALIDATE_URL)){
      return $path;
    }else{
      return self::$config['baseURL'] . "/" . $path;
    }
  }
  
  /**
   * Make Absolute Path from relative path
   */
  public static function getPath($path){
    $path = realpath(self::$config["basePath"] . "/" . $path);
    if($path !== false && self::startsWith($path, self::$config["basePath"])){
      return $path;
    }
    return false;
  }
  
  protected static function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
  
  public static function css($name = null, $url = null){
    if($name === null){
      self::getLinkTag();
    }else{
      self::$css[$name] = $url;
    }
  }
  
  public static function js($name, $url){
    if($name === null){
      self::getScriptTag();
    }else{
      self::$js[$name] = $url;
    }
  }
  
  public static function removeCSS($name){
    if(is_array($name)){
      foreach($name as $v){
        unset(self::$css[$v]);
      }
    }else{
      unset(self::$css[$name]);
    }
    return true;
  }
  
  public static function removeJS($name){
    if(is_array($name)){
      foreach($name as $v){
        unset(self::$js[$v]);
      }
    }else{
      unset(self::$js[$name]);
    }
    return true;
  }
  
  public static function getLinkTag(){
    $html = "";
    foreach(self::$css as $url){
      $html .= "<link rel='stylesheet' href='". self::getURL($url) ."' />";
    }
    return $html;
  }
  
  public static function getScriptTag(){
    $html = "";
    foreach(self::$js as $url){
      $html .= "<script src='". self::getURL($url) ."'></script>";
    }
    return $html;
  }
  
  /**
   * Get the <link tag of combined CSS files
   */
  public static function getServeLinkTag($params = array()){
    return "<link rel='stylesheet' href='". self::getServeURL("css", $params) ."' async='async' defer='defer' />";
  }
  
  public static function getServeScriptTag($params = array()){
    return "<script src='". self::getServeURL("js", $params) ."'></script>";
  }
  
  /**
   * @param $type string - Either "js" or "css"
   * @param $params array - List of extra GET parameters to include in URL
   */
  public static function getServeURL($type, $params = array()){
    $url = self::getURL(self::$config["serveFile"]);
    
    if($type === "css"){
      $url .= "?assets=" . implode(",", self::$css) . "&type=css";
    }else{
      $url .= "?assets=" . implode(",", self::$js) . "&type=js";
    }
    
    if(count($params) !== 0){
      $url .= "&" . http_build_query($params);
    }
    return $url;
  }
  
  /**
   * @param $data string - The Asset File Contents
   */
  public static function preProcess($data, $type){
    if(self::$preProcess === null){
      return $data;
    }else{
      return call_user_func_array(self::$preProcess, array($data, $type));
    }
  }
  
  public static function serve(){
    $assets = isset($_GET['assets']) ? $_GET['assets'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    
    if($type === "css" || $type === "js"){
      
      if($type === "css"){
        header("Content-type: text/css");
      }else{
        header("Content-type: application/x-javascript");
      }
      header("Cache-Control: public");
            
      /**
       * Separate the Assets and remove null items
       */
      $assets = array_filter(explode(",", $assets));
      
      /**
       * Calculate ETag before echoing out the assets
       */
      $etag = "";
      foreach($assets as $assetLocation){
        $assetLocation = self::getPath($assetLocation);
        if($assetLocation !== false){
          $etag .= filemtime($assetLocation);
        }
      }
      
      $etag = hash("md5", $etag);
      header("ETag: $etag");
      
      /**
       * Was it already cached before by the browser ? The old etag will be sent by
       * the browsers as HTTP_IF_NONE_MATCH. '501' is a random value
       */
      $browserTag = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? $_SERVER["HTTP_IF_NONE_MATCH"] : null;
      
      if($browserTag === $etag){
        header("HTTP/1.1 304 Not Modified");
      }else{
        foreach($assets as $assetRelLocation){
          $assetLocation = self::getPath($assetRelLocation);
          
          if($assetLocation === false){
            /**
             * If file doesn't exist or is not under base directory
             */
            if(self::$config["debug"] === true){
              echo "invalid_file - $assetRelLocation";
            }
          }else{
            $data = self::preProcess(file_get_contents($assetLocation), $type);
            
            if($data !== null){
              echo $data;
            }
          }
        }
      }
    }else{
      echo "incompatible_type";
    }
  }
  
  public static function issetJS($asset){
    return isset(self::$js[$asset]);
  }
  
  public static function issetCSS($asset){
    return isset(self::$css[$asset]);
  }
  
  public static function getJS($asset){
    return self::$js[$asset];
  }
  
  public static function getCSS($asset){
    return self::$css[$asset];
  }

}
