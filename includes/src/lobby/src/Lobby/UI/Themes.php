<?php
namespace Lobby\UI;

class Themes extends \Lobby {
  
  /**
   * Cache results
   */
  private static $cache = array();
  
  public static $themeID, $theme;
  
  /**
   * Initialization
   */
  public static function __constructStatic(){
    self::$themeID = getOption("active_theme");
    if(self::$themeID == null){
      self::$themeID = "hine";
    }else if(self::validTheme(self::$themeID) === false){
      self::$themeID = "hine";
    }
    define("THEME_ID", self::$themeID);
    define("THEME_DIR", THEMES_DIR . "/" . self::$themeID);
    define("THEME_URL", THEMES_URL . "/" . self::$themeID);
    
    if(!\Lobby::status("lobby.assets-serve")){
      self::loadDefaults();
      self::loadTheme();
    }
  }
  
  /**
   * Get themes installed
   */
  public static function getThemes(){
    if(!isset(self::$cache["themes"])){
      self::$cache['themes'] = array();
      $theme_folders = array_diff(scandir(THEMESE_DIR), array('..', '.'));
    
      foreach($theme_folders as $theme_folder_name){
        if(self::valid($theme_folder_name)){
          self::$cache['themes'][$theme_folder_name] = 1;
        }
      }
    }
    return self::$cache['themes'];
  }
  
  /**
   * Load Default CSS & JS
   */
  public static function loadDefaults(){
    /**
     * Scripts
     */
    \Assets::js("jquery", "/includes/lib/jquery/jquery.js");
    \Assets::js("jqueryui", "/includes/lib/jquery/jquery-ui.js"); // jQuery UI
    \Assets::js("main", "/includes/lib/lobby/js/main.js");
    
    if(\Lobby::$installed){
      \Assets::js("notify", "/includes/lib/lobby/js/notify.js");
    }
  }
  
  /**
   * Load a theme
   */
  public static function loadTheme(){
    
    require_once THEME_DIR . "/Theme.php";
    
    $className = "\Lobby\UI\Themes\\" . self::$themeID;
    self::$theme = new $className();
    
    self::$theme->init();
    
    /**
     * Load Panel
     */
    if(\Lobby::status("lobby.admin")){
      self::$theme->addStyle("/src/main/css/style.css");
      \Lobby::hook("admin.head.begin", function(){
        self::$theme->panel(true);
        self::$theme->addStyle("/src/main/css/admin.style.css");
      });
      \Lobby::hook("admin.body.begin", function() {
        echo self::$theme->inc("/src/panel/load.admin.php");
      });
    }else{
      self::$theme->addStyle("/src/main/css/style.css");
      \Lobby::hook("head.begin", function(){
        self::$theme->panel(false);
      });
      \Lobby::hook("body.begin", function() {
        echo self::$theme->inc("/src/panel/load.php");
      });
    }
    
  }
  
  /**
   * Load Dashboard
   */
  public static function loadDashboard($dashboard_items){
    if($dashboard_items == "head"){
      self::$theme->dashboard();
    }else{
      echo self::$theme->inc("/src/dashboard/load.php");
    }
  }
  
  /**
   * Check if a theme is valid
   */
  public static function validTheme($theme){
    $valid = false;
    $loc = THEMES_DIR . "/$theme";
    
    /**
     * Does the "Theme.php" file in theme directory exist ?
     */
    if(file_exists("$loc/src/dashboard/load.php") && file_exists("$loc/src/panel/load.php")){
      $valid = true;
    }
    return $valid;
  }
  
}
