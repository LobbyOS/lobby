<?php
namespace Lobby;

class App {
  public $dir, $url, $id, $srcURL;
  
  public function setTheVars($array){
    $this->id   = $array['id'];
    $this->name = $array['name'];
    $this->URL  = $array['URL'];
    $this->srcURL = $array['srcURL'];
  }
  
  public function addStyle($fileName){
    $url = "/contents/apps/{$this->id}/src/CSS/$fileName";
    \Lobby::addStyle("{$this->id}-{$fileName}", $url);
  }
  
  public function addScript($fileName){
    $url = "/contents/apps/{$this->id}/src/JS/$fileName";
    \Lobby::addScript("{$this->id}-{$fileName}", $url);
  }
  
  public function setTitle($title){
    \Lobby::setTitle("$title | {$this->name}");
  }
  
  public static function u($path){
    return APP_URL . $path;
  }
  
  public static function l($path, $text = "", $extra = ""){
    return \Lobby::l(APP_URL . $path, $text, $extra);
  }
  
  public static function get($path){
    return \Lobby\FS::get(APP_DIR . $path);
  }
  
  /**
   * Include a page from the app's source
   */
  public function inc($path, $vars = array()){
    $app_dir_location = APP_DIR . $path;
    if(!file_exists($app_dir_location)){
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
        include $app_dir_location;
      $html = ob_get_clean();
    
      return $html;
    }
  }
}
?>
