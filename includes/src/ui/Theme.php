<?php
namespace Lobby\UI;

class Theme {

  /**
   * Tell Lobby to load a stylesheet
   */
  public function addStyle($file_location){
    $url = "/contents/themes/". THEME_ID . $file_location;
    \Lobby::addStyle("theme.". THEME_ID ."-{$file_location}", $url);
  }
  
  /**
   * Tell Lobby to load a script
   */
  public function addScript($file_location){
    $url = "/contents/themes/". THEME_ID . $file_location;
    \Lobby::addScript("theme.". THEME_ID ."-{$file_location}", $url);
  }
  
  /**
   * Include a page from the theme's source
   */
  public function inc($path, $vars = array()){
    $theme_file_location = THEME_DIR . $path;

    if(!file_exists($theme_file_location)){
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
        include $theme_file_location;
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
