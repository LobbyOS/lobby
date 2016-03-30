<?php
/**
 * \Lobby\App
 * Associated with all kinds of operations with apps
 */

namespace Lobby;

class Apps extends \Lobby {

  private $app = false;
  public $appDir = false, $exists = false, $info = array(), $enabled = false;
  
  /**
   * Cache frequently used data
   */
  public static $cache = array(
    "valid_apps" => array()
  );
  
  public static function clearCache(){
    self::$cache = array(
      "valid_apps" => array()
    );
  }

  /**
   * Get the apps that are in the directory as array
   */
  public static function getApps(){
    if(isset(self::$cache["apps"])){
      $apps = self::$cache["apps"];
    }else{
      $appFolders = array_diff(scandir(APPS_DIR), array('..', '.'));
      $apps = array();
    
      foreach($appFolders as $appFolderName){
        if(self::valid($appFolderName)){
          $apps[] = $appFolderName;
        }
      }
      self::$cache["apps"] = $apps;
    }
    return $apps;
  }
  
  /**
   * Returns enabled apps as an array
   */
  public static function getEnabledApps(){
    if(isset(self::$cache["enabled_apps"])){
      $enabled_apps = self::$cache["enabled_apps"];
    }else{
      $enabled_apps = getOption("enabled_apps");
      $enabled_apps = json_decode($enabled_apps, true);
      
      if(!is_array($enabled_apps) || count($enabled_apps) == 0){
        $enabled_apps = array();
      }
      self::$cache["enabled_apps"] = $enabled_apps;
    }
    return $enabled_apps;
  }
 
  /**
   * Returns disabled apps as an array
   */
  public static function getDisabledApps(){
    if(isset(self::$cache["disabled_apps"])){
      $disabled_apps = self::$cache["disabled_apps"];
    }else{
      $disabled_apps = array();
      $enabledApps = self::getEnabledApps();

      foreach(self::getApps() as $app){
        if(array_search($app, $enabledApps) === false){
          $disabled_apps[] = $app;
        }
      }
      self::$cache["disabled_apps"] = $disabled_apps;
    }
    return $disabled_apps;
  }
  
  /**
   * Check if an app exists
   */
  public static function exists($app){
    $apps = self::getApps();
    return in_array($app, $apps, true);
  }
  
  /**
   * Check if App is valid and it meets criteria of Lobby
   */
  public static function valid($name = ""){
    if(isset(self::$cache["valid_apps"][$name])){
      $valid = self::$cache["valid_apps"][$name];
    }else{
      $appDir = APPS_DIR . "/$name";
      $valid = false;
    
      /**
       * Initial checking
       */
      if( is_dir($appDir) && file_exists("$appDir/manifest.json") ){
        $valid = true;
      }
      if( $valid === true && file_exists("$appDir/App.php") ){
        /**
         * Make sure the App class exists
         */
        require_once L_DIR . "/includes/src/App.php";
        require_once "$appDir/App.php";
      
        $className = "\\Lobby\App\\" . str_replace("-", "_", $name);
        if( !class_exists($className) ){
          $valid = false; // The class doesn't exist, so app's not valid
        }else{
          $class = new $className;
          if (!is_subclass_of($class, '\Lobby\App') || !method_exists($class, 'page')){
            $valid = false;
          }
        }
      }else{
        $valid = false; // The App.php file is not found
      }
      self::$cache["valid_apps"][$name] = $valid;
    }
    return $valid;
  }
  
  /**
   * Make an object of App
   */
  public function __construct($id = ""){
    if($id != ""){
      if(self::valid($id)){
        $this->exists = true;
        $appDir = APPS_DIR . "/$id";
        $this->app = $id;
        $this->appDir = $appDir;
        
        /**
         * App Manifest Info as a object variable
         */
        $this->setInfo();
        return true;
      }else{
        $this->exists = false;
        if($this->disableApp()){
          $this->log("App $name was disabled because it was not a valid App.");
        }
        return false;
      }
    }
  }
 
  /**
   * Get the manifest info of app as array
   */
  private function setInfo(){
    $manifest = file_exists($this->appDir . "/manifest.json") ?
    file_get_contents($this->appDir . "/manifest.json") : false;
    
    if($manifest){
      $details = json_decode($manifest, true);
      /**
       * Add extra info with the manifest info
       */
      $details['id'] = $this->app;
      $details['location'] = $this->appDir;
      $details['URL'] = L_URL . "/app/{$this->app}";
      $details['srcURL'] = L_URL . "/contents/apps/{$this->app}";
      $details['adminURL'] = L_URL . "/admin/app/{$this->app}";
      
      /**
       * Prefer SVG over PNG
       */
      $details['logo'] = isset($details['logo']) ?
        (file_exists($this->appDir . "/src/image/logo.svg") ?
          APPS_URL . "/{$this->app}/src/image/logo.svg" :
          APPS_URL . "/{$this->app}/src/image/logo.png"
        ) : null;
       
      /**
       * Insert the info as a property
       */
      $this->info = $details;
      
      /**
       * Whether app is enabled
       */
      $this->enabled = in_array($this->app, self::getEnabledApps(), true);
      
      return $details;
    }else{
      return false;
    }
  }
 
  /**
   * Enable the app
   */
  public function enableApp(){
    if($this->app){
      $apps = self::getEnabledApps();
      if(!in_array($this->app, $apps, true)){
        $apps[] = $this->app;
        
        saveOption("enabled_apps", json_encode($apps));
        self::clearCache();
        return true;
      }else{
        return true; // App Is Already Enabled. So we don't need give out the boolean false.
      }
    }else{
      return false;
    }
  }
 
  /**
   * Disable the app
   */
  public function disableApp(){
    if($this->app && $this->enabled){
      $apps = self::getEnabledApps();

      if(in_array($this->app, $apps, true)){
        $key = array_search($this->app, $apps);
        unset($apps[$key]);
        
        saveOption("enabled_apps", json_encode($apps));
        self::clearCache();
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
 
  /**
   * Disable app and remove the app files recursilvely from the directory
   */
  public function removeApp(){
    if($this->app){
      if(self::exists($this->app) !== false){
        $dir = $this->appDir;
        if(file_exists("$dir/uninstall.php")){
          include_once "$dir/uninstall.php";
        }
        
        $this->disableApp();
        return \Lobby\FS::remove($dir);
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
 
  /**
   * Return the app class object
   */
  public function run(){
    if($this->app){
      require_once L_DIR . "/includes/src/App.php";
      require_once $this->appDir . "/App.php";
      
      \Lobby::addScript("app", "/includes/lib/lobby/js/app.js");
      
      $GLOBALS['AppID'] = $this->app;
     
      $appInfo = $this->info;
      $className = "\\Lobby\App\\" . str_replace("-", "_", $this->app);
     
      /**
       * Create the \Lobby\App Object
       */
      $class = new $className;
     
      /**
       * Send app details to the App Object
       */
      $class->setTheVars($appInfo);
      
      /**
       * Define the App Constants
       */
      define("APP_DIR", $this->appDir);
      define("APP_SRC", $this->info['srcURL']);
      if(!defined("APP_URL")){
        /**
         * We specifically check if APP_URL is defined,
         * because it may be already defined by `singleapp` module.
         */
        define("APP_URL", $this->info['URL']);
        define("APP_ADMIN_URL", $this->info['adminURL']);
      }
     
      /* Return the App Object */
      return $class;
    }
  }
}
?>
