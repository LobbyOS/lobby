<?php
namespace Lobby;

use Lobby;
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
   * URL to apps directory
   */
  private static $appsURL = null;
  
  /**
   * App Updates
   * App => $latestVersion
   */
  private static $appUpdates = array();
  
  /**
   * This will contain the App object when app is running
   */
  private static $activeApp = false;
  private static $activeAppInstance = false;
  
  /**
   * Default values in manifest.json
   */
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
  
  /**
   * @param array $appsVARS Contains path and URL to `apps` folder
   */
  public static function __constructStatic($appsVARS){
    self::$appsDir = $appsVARS[0];
    self::$appsURL = Lobby::u($appsVARS[1]);
    
    /**
     * Make array like this :
     * "AppID" => 0
     */
    $appsAsKeys = array_flip(self::getApps());
    array_walk($appsAsKeys, function(&$val){
      $val = 0;
    });
    
    self::$appUpdates = array_replace_recursive($appsAsKeys, DB::getJSONOption("app_updates"));
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
        if(self::valid($appFolderName, false)){
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
   * @param string $appID The Apps' ID
   * @param bool $basicCheck Whether it should be a simple/basic check
   */
  public static function valid($appID = "", $basicCheck = true){
    if(isset(self::$cache["valid_apps"][$appID]) && $basicCheck){
      $valid = self::$cache["valid_apps"][$appID];
    }else{
      $appDir = self::$appsDir . "/$appID";
      $valid = false;
    
      /**
       * Check if App.php and manifest file exist
       */
      if( FS::exists("$appDir/manifest.json") && FS::exists("$appDir/App.php") ){
        $valid = true;
      }
      
      if($valid && !$basicCheck){
        /**
         * Make sure the App class exists
         */
        require_once "$appDir/App.php";
        
        $className = "\\Lobby\App\\" . self::normalizeID($appID);
        if( !class_exists($className) ){
          $valid = false; // The class doesn't exist, so app's not valid
        }else{
          $class = new $className;
          if (!is_subclass_of($class, '\Lobby\App') || !method_exists($class, 'page')){
            $valid = false;
          }
        }
        
        $manifest = json_decode(FS::get("$appDir/manifest.json"), true);
        if(!is_array($manifest) || (isset($manifest["require"]) && !Need::checkRequirements($manifest["require"], true))){
          $valid = false;
        }
      }
      
      self::$cache["valid_apps"][$appID] = $valid;
    }
    return $valid;
  }
  
  /**
   * Make an object of App
   */
  public function __construct($id){
    $this->app = $id;
    
    if(self::valid($id, false)){
      $this->exists = true;
      $this->dir = self::$appsDir . "/$id";
      
      /**
       * App Manifest Info as a object property
       */
      $this->setInfo();
      return true;
    }else{
      if($this->disableApp())
        Lobby::log("'". $this->info["name"] ."' is not a valid app.");
      $this->app = false;
      return false;
    }
  }
 
  /**
   * Get the manifest info of app as array
   */
  private function setInfo(){
    $manifest = FS::exists($this->dir . "/manifest.json") ?
      FS::get($this->dir . "/manifest.json") : false;
    
    if($manifest){
      $details = json_decode($manifest, true);
      $details = array_replace_recursive(self::$manifestConfig, $details);
      
      /**
       * Add extra info with the manifest info
       */
      $details['id'] = $this->app;
      $details['dir'] = $this->dir;
      $details['url'] = Lobby::getURL() . "/app/{$this->app}";
      $details['srcURL'] = Lobby::getURL() . "/contents/apps/{$this->app}";
      $details['adminURL'] = Lobby::getURL() . "/admin/app/{$this->app}";
      
      /**
       * Prefer SVG over PNG
       */
      $details['logo'] = $details['logo'] !== false ?
        (FS::exists($this->dir . "/src/image/logo.svg") ?
          self::$appsURL . "/{$this->app}/src/image/logo.svg" :
          self::$appsURL . "/{$this->app}/src/image/logo.png"
        ) : Themes::getThemeURL() . "/src/main/image/app-logo.png";
      
      $details["latestVersion"] = isset(self::$appUpdates[$this->app]) ? self::$appUpdates[$this->app] : null;
      
      $details = \Hooks::applyFilters("app.manifest", $details);
       
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
    if($this->app){
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
     * Also remove JSON syntax
     */
    $result = json_encode($result);
    $result = str_replace("[roeEcvv,]", null, $result);
    
    return mb_strlen($result);
  }
  
  /**
   * Whether app update is available
   * Provide $latestVersion to check if it's a latest version
   */
  public function hasUpdate($latestVersion = null){
    if($latestVersion !== null)
      return version_compare($this->info['version'], $latestVersion, "<");
    else
      return version_compare($this->info['version'], $this->info['latestVersion'], "<");
  }
  
  public function clearData(){
    $sql = \Lobby\DB::getDBH()->prepare("DELETE FROM `". \Lobby\DB::getPrefix() ."data` WHERE `app` = ?");
    $sql->execute(array($this->app));
    return true;
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
      $class->setAppInfo($this->info);
      
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
      self::$activeAppInstance = $this->getInstance();
      
      /**
       * Return the App Object
       */
      return self::$activeAppInstance;
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
  
  public static function getRunningInstance(){
    return self::$activeAppInstance;
  }
  
}
