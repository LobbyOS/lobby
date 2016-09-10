<?php
namespace Lobby\UI;

use Lobby\UI\Themes;

class Theme {

  protected $id, $dir;

  public function __construct($themeID, $themeDir){
    $this->id = $themeID;
    $this->dir = $themeDir;
  }

  /**
   * Tell Lobby to load a stylesheet
   */
  public function addStyle($file_location){
    $url = "/contents/themes/". $this->id . $file_location;
    \Assets::css("theme.". $this->id ."-{$file_location}", $url);
  }

  /**
   * Tell Lobby to load a script
   */
  public function addScript($file_location){
    $url = "/contents/themes/". $this->id . $file_location;
    \Assets::js("theme.". $this->id ."-{$file_location}", $url);
  }

  /**
   * Include a page from the theme's source
   */
  public function inc($path, $vars = array()){
    $themeFileLocation = $this->dir . $path;

    if(!file_exists($themeFileLocation)){
      return false;
    }else{
      /**
       * Define variables for the file
       */
      if(count($vars) != 0){
        extract($vars);
      }

      /**
       * Get the output of the file in a variable
       */
      ob_start();
        include $themeFileLocation;
      $html = ob_get_clean();

      return $html;
    }
  }

  /**
   * Write messages to log file
   */
  public function log($msg){
    \Lobby::log($msg, "themes.log");
  }

  /**
   * Called before panel is made
   */
  public function panel($isAdmin){}

  /**
   * Called before dashboard is made
   */
  public function dashboard(){}

  /**
   * Called before theme is loaded
   */
  public function init(){}

}
