<?php
/**
 * \Lobby\App
 * Associated with all kinds of operations with apps
 */

namespace Lobby;

class Apps extends \Lobby {

  private $app = false;
  public $appDir = false;
  public $exists = false;
  public $info = array();
  
  /**
   * To make Lobby faster
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
      $enabled_apps = getOption("active_apps");
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
    return in_array($app, $apps);
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
   * Returns boolean of installation status
   */
  public function isEnabled(){
    if(isset(self::$cache["enabled_apps"][$this->app])){
      $enabled = self::$cache["enabled_apps"][$this->app];
    }else{
      $enabledApps = self::getEnabledApps();
      $enabled = isset($enabledApps[$this->app]);
      if($enabled){
        self::$cache["enabled_apps"][$this->app] = $enabled;
      }
    }
    return $enabled;
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
      
      $details['logo'] = isset($details['logo']) ?
        APPS_URL . "/{$this->app}/src/image/logo.png" :
        L_URL . "/includes/lib/lobby/image/blank.png";
       
      /**
       *Insert the info as a property
       */
      $this->info = $details;
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
      if(!in_array($this->app, $apps)){
        $apps[] = $this->app;
        
        saveOption("active_apps", json_encode($apps));
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
    if($this->app && $this->isEnabled()){
      $apps = self::getEnabledApps();
      
      if(!in_array($this->app, $apps)){
        unset($apps[array_search($this->app, $apps)]);
        
        saveOption("active_apps", json_encode($apps));
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
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) { 
          if(is_dir("$dir/$file")){
            delTree("$dir/$file");
          }else{
            \Lobby\FS::remove("$dir/$file");
          }
        } 
        return rmdir($dir);
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
