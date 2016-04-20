<?php
namespace Lobby;

class App {

  public $dir, $url, $id, $srcURL;
  
  public function setTheVars($array){
    $this->id   = $array['id'];
    $this->name = $array['name'];
    $this->URL  = $array['URL'];
    $this->srcURL = $array['srcURL'];
    $this->dir = $array['location'];
    $this->manifest = $array;
  }
  
  public function addStyle($fileName){
    $url = "/contents/apps/{$this->id}/src/css/$fileName";
    \Assets::css("{$this->id}-{$fileName}", $url);
  }
  
  public function addScript($fileName){
    $url = "/contents/apps/{$this->id}/src/js/$fileName";
    \Assets::js("{$this->id}-{$fileName}", $url);
  }
  
  public function setTitle($title){
    \Lobby::setTitle("$title | {$this->name}");
  }
  
  /**
   * Get Data
   */
  public function getData($key = "", $extra = false){
    return \Lobby\DB::getData($this->id, $key, $extra);
  }
  
  /**
   * Get JSON Data
   */
  public function getJSONData($key = ""){
    return \H::getJSONData($key, $this->id);
  }
  
  /**
   * Push a notify item
   */
  public function addNotifyItem($id, $info){
    if(!isset($info["href"])){
      $info["href"] = $this->URL;
    }
    return \Lobby\UI\Panel::addNotifyItem("app_{$this->id}_$id" , $info);
  }
  
  /**
   * Remove a notify item
   */
  public function removeNotifyItem($id){
    return \Lobby\UI\Panel::removeNotifyItem("app_{$this->id}_$id");
  }
  
  public static function u($path = null){
    return APP_URL . $path;
  }
  
  public static function l($path, $text = "", $extra = ""){
    return \Lobby::l(APP_URL . $path, $text, $extra);
  }
  
  public static function get($path){
    return \Lobby\FS::get(APP_DIR . $path);
  }
  
  public static function write($path, $content, $type = "w"){
    return \Lobby\FS::write(APP_DIR . $path, $content, $type);
  }
  
  public static function redirect($path){
    return \Lobby::redirect(self::u($path));
  }
  
  /**
   * Include a page from the app's source
   */
  public function inc($path, $vars = array()){
    $app_file_location = $this->dir . "/" . $path;

    if(!file_exists($app_file_location)){
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
        include $app_file_location;
      $html = ob_get_clean();
    
      return $html;
    }
  }
  
  public function page($page){
    return "auto";
  }
  
  /**
   * Write messages to log file
   */
  public function log($msg){
    \Lobby::log($msg, "app.". $this->id . ".log");
  }
  
}
