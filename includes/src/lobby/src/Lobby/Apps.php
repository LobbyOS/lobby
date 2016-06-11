<?php
namespace Lobby;

use Lobby\DB;
use Lobby\FS;
use Lobby\Need;
use Lobby\UI\Themes;

/**
 * \Lobby\Apps
 * Associated with all kinds of operations with apps
 */

class Apps {
  
  /**
   * Location of apps directory
   */
  private static $appsDir = null;
  
  /**
   * This will contain the App object when app is running
   */
  private static $activeApp = false;
  
  protected static $manifestConfig = array(
    "name" => "",
    "short_description" => "",
    "category" => "",
    "sub_category" => "",
    "version" => "0",
    "author" => "",
    "author_page" => "",
    "logo" => false,
    "app_page" => "",
    "require" => array()
  );
  
  /**
   * Cache frequently used data
   */
  public static $cache = array(
    "valid_apps" => array()
  );
  
  /**
   * The App ID
   */
  private $app = false;
  
  public $appDir = false, $exists = false, $info = array(), $enabled = false;
  
  public static function __constructStatic($appsDir){
    self::$appsDir = $appsDir;
  }
  
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
      $appFolders = array_diff(scandir(self::$appsDir), array('..', '.'));
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
      $enabled_apps = DB::getOption("enabled_apps");
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
  
  public static function getAppsDir(){
    return self::$appsDir;
  }
  
  /**
   * Check if an app exists
   */
  public static function exists($app){
    $apps = self::getApps();
    return in_array($app, $apps, true);
  }
  
  /**
   * Make App ID into Class Name
   */
  public static function normalizeID($appID){
    $appID = str_replace("-", "_", $appID);
    return $appID;
  }
  
  /**
   * Check if App is valid and it meets criteria of Lobby
   */
  public static function valid($name = ""){
    if(isset(self::$cache["valid_apps"][$name])){
      $valid = self::$cache["valid_apps"][$name];
    }else{
      $appDir = self::$appsDir . "/$name";
      $valid = false;
    
      /**
       * Check if app directory exist and that manifest file has valid JSON
       */
      if( is_dir($appDir) && is_array(json_decode(file_get_contents("$appDir/manifest.json"), true)) ){
        $valid = true;
      }
      
      if( $valid === true && file_exists("$appDir/App.php") ){
        /**
         * Make sure the App class exists
         */
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
  public function __construct($id = null){
    if($id !== null){
      if(self::valid($id)){
        $this->exists = true;
        $this->app = $id;
        $this->dir = self::$appsDir . "/$id";
        
        /**
         * App Manifest Info as a object property
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
    $manifest = FS::exists($this->dir . "/manifest.json") ?
      file_get_contents($this->dir . "/manifest.json") : false;
    
    if($manifest){
      $details = json_decode($manifest, true);
      $details = array_replace_recursive(self::$manifestConfig, $details);
      
      /**
       * Add extra info with the manifest info
       */
      $details['id'] = $this->app;
      $details['location'] = $this->dir;
      $details['url'] = L_URL . "/app/{$this->app}";
      $details['srcURL'] = L_URL . "/contents/apps/{$this->app}";
      $details['adminURL'] = L_URL . "/admin/app/{$this->app}";
      
      /**
       * Prefer SVG over PNG
       */
      $details['logo'] = $details['logo'] !== false ?
        (FS::exists($this->dir . "/src/image/logo.svg") ?
          APPS_URL . "/{$this->app}/src/image/logo.svg" :
          APPS_URL . "/{$this->app}/src/image/logo.png"
        ) : Themes::getURL() . "/src/main/image/app-logo.png";
       
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
        
        DB::saveOption("enabled_apps", json_encode($apps));
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
        
        DB::saveOption("enabled_apps", json_encode($apps));
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
        $dir = $this->dir;
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
   * Check requirements of app
   */
  public function checkRequirements(){
    if($this->app && isset($this->info["requires"])){
      return Need::checkRequirements($this->info["requires"], true);
    }
  }
  
  /**
   * Get size used in database
   */
  public function getDBSize($normalizeSize = false){
    $sql = \Lobby\DB::getDBH()->prepare("SELECT * FROM `". \Lobby\DB::getPrefix() ."data` WHERE `app` = ?");
    $sql->execute(array($this->app));
    $result = $sql->fetchAll(\PDO::FETCH_ASSOC);
    
    /**
     * Convert array values to string
     */
    $result = json_encode($result);
    $result = str_replace("[roeEcvv,]", null, $result);
    
    $tmpFile = FS::getTempFile();
    FS::write($tmpFile, $result);
    $size = FS::getSize($tmpFile, $normalizeSize);
    FS::remove($tmpFile);
    
    return $size;
  }
  
  /**
   * Get the App object
   */
  public function getInstance(){
    if($this->app){
      /**
       * Load the app class
       */
      require_once $this->dir . "/App.php";
      
      $className = "\\Lobby\App\\" . self::normalizeID($this->app);
     
      /**
       * Create the \Lobby\App Object
       */
      $class = new $className;
     
      /**
       * Send app details to the App Object
       */
      $class->setTheVars($this->info);
      
      return $class;
    }
  }
 
  /**
   * Return the app class object
   */
  public function run(){
    if($this->app){
      \Assets::js("app", "/includes/lib/lobby/js/app.js");
      
      self::$activeApp = $this;
      
      /**
       * Return the App Object
       */
      return $this->getInstance();
    }
  }
  
  /**
   * Get config value of currently running app
   */
  public static function getInfo($key){
    if(self::$activeApp)
      return self::$activeApp->info[$key];
    else
      return null;
  }
  
  /**
   * Whether Lobby is in app-mode
   */
  public static function isAppRunning(){
    return self::$activeApp;
  }
  
}
