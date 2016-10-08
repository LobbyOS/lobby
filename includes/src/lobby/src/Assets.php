<?php
/**
 * Manage and serve assets
 * Contains an independent class
 */

/**
 * Assets Manager
 */
class Assets {

  /**
   * The CSS & JS assets
   */
  protected static $css = array(), $js = array();

  /**
   * The configuration
   */
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

  /**
   * Set the configuration
   * @param array $config Array containg the configuration
   */
  public static function config($config){
    self::$config = array_replace_recursive(self::$config, $config);
    self::$config["basePath"] = realpath(self::$config["basePath"]);
  }

  /**
   * Make URL from path
   * @param string $path Relative path
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
   * @param string $path Get absolute location to a file. Returns false if path is outside the base directory
   */
  public static function getPath($path){
    $path = realpath(self::$config["basePath"] . "/" . $path);
    if($path !== false && self::startsWith($path, self::$config["basePath"])){
      return $path;
    }
    return false;
  }

  /**
   * Whether a string starts with a particular string
   * @param string $string The string where the check should be done
   * @param string $needle The string to match
   */
  protected static function startsWith($string, $needle){
    $length = strlen($needle);
    return (substr($string, 0, $length) === $needle);
  }

  /**
   * Add a CSS asset or return the <link> tag
   * @param string $name Asset's name. If this is null, <link href='... tag is returned
   * @param $url $url Path to the CSS file
   */
  public static function css($name = null, $url = null){
    if($name === null){
      self::getLinkTag();
    }else{
      self::$css[$name] = $url;
    }
  }

  /**
   * Add a JS asset or return the <script> tag
   * @param string $name Asset's name. If this is null, <script src='... tag is returned
   * @param $url $url Path to the JS file
   */
  public static function js($name, $url){
    if($name === null){
      self::getScriptTag();
    }else{
      self::$js[$name] = $url;
    }
  }

  /**
   * Remove a CSS asset
   * @param string $name The name of asset to remove
   */
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

  /**
   * Remove a JS asset
   * @param string $name The name of asset to remove
   */
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

  /**
   * Get the <link href> tag of all assets
   */
  public static function getLinkTag(){
    $html = "";
    foreach(self::$css as $url){
      $html .= "<link rel='stylesheet' href='". self::getURL($url) ."' />";
    }
    return $html;
  }

  /**
   * Get the <script src> tag of all assets
   */
  public static function getScriptTag(){
    $html = "";
    foreach(self::$js as $url){
      $html .= "<script src='". self::getURL($url) ."'></script>";
    }
    return $html;
  }

  /**
   * @param array $params Extra GET parameters in URL
   * @return array Assets with url to serve assets file
   */
  public static function getServeScriptURL($params = array()){
    $assets = array();

    foreach (self::$js as $asset => $null) {
      $assets[$asset] = self::getServeURL("js", $params, array($asset));
    }

    return $assets;
  }

  /**
   * Get the <link src> tag of combined CSS files
   * @param array $params Extra GET data to include in URL
   */
  public static function getServeLinkTag($params = array()){
    return "<link rel='stylesheet' href='". self::getServeURL("css", $params) ."' async='async' defer='defer' />";
  }

  /**
   * Get the <script src> tag of combined JS files
   * @param array $params Extra GET data to include in URL
   */
  public static function getServeScriptTag($params = array()){
    return "<script src='". self::getServeURL("js", $params) ."'></script>";
  }

  /**
   * Get the URL to the file that serves the assets
   * @param $type string - Either "js" or "css"
   * @param $params array - List of extra GET parameters to include in URL
   * @param $customAssets array - Only include some assets
   */
  public static function getServeURL($type, $params = array(), $customAssets = array()){
    $url = self::getURL(self::$config["serveFile"]);

    if($type === "css"){
      $assets = self::$css;
    }else{
      $assets = self::$js;
    }

    if(!empty($customAssets)){
      foreach($assets as $asset => $assetLocation){
        if(!in_array($asset, $customAssets))
          unset($assets[$asset]);
      }
    }

    if($type === "css"){
      $url .= "?assets=" . implode(",", $assets) . "&type=css";
    }else{
      $url .= "?assets=" . implode(",", $assets) . "&type=js";
    }

    if(count($params) !== 0){
      $url .= "&" . http_build_query($params);
    }
    return $url;
  }

  /**
   * Pre process the code inside asset
   * @param string $data Code inside asset
   * @param string $type The asset type (js/css)
   */
  public static function preProcess($data, $type){
    if(self::$preProcess === null){
      return $data;
    }else{
      return call_user_func_array(self::$preProcess, array($data, $type));
    }
  }

  /**
   * Handle the request to serve assets and respond with assets
   */
  public static function serve(){
    $assets = isset($_GET['assets']) ? $_GET['assets'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;

    if($type === "css" || $type === "js"){

      if($type === "css"){
        Response::header()->set("Content-Type", "text/css");
      }else{
        Response::header()->set("Content-Type", "application/x-javascript");
      }

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

      $etag = md5($etag);
      Response::setCache(array(
        "etag" => $etag,
        "private" => false,
        "public" => true
      ));

      /**
       * Was it already cached before by the browser ? The old etag will be sent by
       * the browsers as HTTP_IF_NONE_MATCH. '501' is a random value
       */
      $browserTag = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? $_SERVER["HTTP_IF_NONE_MATCH"] : null;

      if($browserTag === $etag){
        Response::setStatusCode(304);
      }else{
        foreach($assets as $assetRelLocation){
          $assetLocation = self::getPath($assetRelLocation);

          if($assetLocation === false){
            /**
             * If file doesn't exist or is not under base directory
             */
            if(self::$config["debug"] === true){
              echo "\n /** Invalid File - $assetRelLocation */\n";
            }
          }else{
            $data = self::preProcess(file_get_contents($assetLocation), $type);

            if($data !== null){
              if(self::$config["debug"] === true)
                echo "\n/** Asset - $assetRelLocation */\n";
              echo $data;
            }
          }
        }
      }
    }else{
      echo "incompatible_type";
    }
  }

  /**
   * Whether a JS asset is added
   * @param string $asset Name of asset to check
   */
  public static function issetJS($asset){
    return isset(self::$js[$asset]);
  }

  /**
   * Whether a CSS asset is added
   * @param string $asset Name of asset to check
   * @return bool
   */
  public static function issetCSS($asset){
    return isset(self::$css[$asset]);
  }

  /**
   * Get the path to a JS asset or return all JS assets
   * @param string $asset Name of asset to check.
   * @return string|array Path to asset if $asset is mentioned or all JS assets
   */
  public static function getJS($asset = null){
    return $asset === null ? self::$js : self::$js[$asset];
  }

  /**
   * Get the path to a CSS asset or return all CSS assets
   * @param string $asset Name of asset to check.
   * @return string|array Path to asset if $asset is mentioned or all CSS assets
   */
  public static function getCSS($asset = null){
    return $asset === null ? self::$css : self::$css[$asset];
  }

}
