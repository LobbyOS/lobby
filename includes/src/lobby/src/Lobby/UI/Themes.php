<?php
namespace Lobby\UI;

use Hooks;
use Lobby;
use Lobby\DB;
use Lobby\FS;

class Themes {

  /**
   * Cache results
   */
  protected static $cache = array();

  /**
   * Path & URL to `themes` folder
   */
  protected static $themesDir;
  protected static $themesURL;

  /**
   * Active theme's Info
   */
  private static $themeID = null, $dir, $url;

  /**
   * Active theme's \Lobby\UI\Theme object
   */
  private static $theme;

  /**
   * Initialization
   * @param array $themeVARS Contains directory and URL to `themes` folder
   */
  public static function __constructStatic($themesVARS){
    self::$themesDir = $themesVARS[0];
    self::$themesURL = Lobby::u($themesVARS[1]);

    self::$themeID = DB::getOption("active_theme");

    /**
     * Default theme is `hine`
     */
    if(self::$themeID === null){
      self::$themeID = "hine";
    }else if(self::validTheme(self::$themeID) === false){
      self::$themeID = "hine";
    }

    self::$url = self::$themesURL . "/" . self::$themeID;
    self::$dir = self::$themesDir . "/" . self::$themeID;

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

  public static function getThemeID(){
    return self::$themeID;
  }

  public static function getThemeDir(){
    return self::$dir;
  }

  public static function getThemeURL(){
    return self::$url;
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

    require_once self::$dir . "/Theme.php";

    $className = "\Lobby\UI\Themes\\" . self::$themeID;
    self::$theme = new $className(self::$themeID, self::$dir);

    self::$theme->init();

    self::$theme->addStyle("/src/main/css/style.css");
    self::$theme->addStyle("/src/main/css/icons.css");

    /**
     * Load Panel
     */
    if(\Lobby::status("lobby.admin")){
      Hooks::addAction("admin.head.begin", function(){
        self::$theme->panel(true);
        self::$theme->addStyle("/src/main/css/admin.style.css");
      });
      \Hooks::addAction("admin.body.begin", function() {
        echo self::$theme->inc("/src/panel/load.admin.php");
      });
    }else{
      \Hooks::addAction("head.begin", function(){
        self::$theme->panel(false);
      });
      \Hooks::addAction("body.begin", function() {
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
    $loc = self::$themesDir . "/$theme";

    /**
     * Does the "Theme.php" file in theme directory exist ?
     */
    if(file_exists("$loc/src/dashboard/load.php") && file_exists("$loc/src/panel/load.php")){
      $valid = true;
    }
    return $valid;
  }

}
