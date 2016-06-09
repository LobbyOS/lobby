<?php
namespace Lobby;

use Response;
use Lobby\DB;

class App {

  public $dir, $url, $id, $srcURL;
  
  public function setTheVars($array){
    $this->id = $array['id'];
    $this->name = $array['name'];
    $this->url = $array['url'];
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
    Response::setTitle("$title | {$this->name}");
  }
  
  /**
   * Get Data
   */
  public function getData($key = "", $extra = false){
    return DB::getData($this->id, $key, $extra);
  }
  
  /**
   * Get JSON Data
   */
  public function getJSONData($key = ""){
    return \H::getJSONData($key, $this->id);
  }
  
  /**
   * Save Data
   */
  public function saveData($key = "", $extra = false){
    return DB::saveData($this->id, $key, $extra);
  }
  
  public function removeData($key){
    return DB::removeData($this->id, $key);
  }
  
  /**
   * Save JSON Data
   */
  public function saveJSONData($key, $values){
    return \H::saveJSONData($key, $values, $this->id);
  }
  
  /**
   * Push a notify item
   */
  public function addNotifyItem($id, $info){
    if(!isset($info["href"])){
      $info["href"] = $this->url;
    }
    return \Lobby\UI\Panel::addNotifyItem("app_{$this->id}_$id" , $info);
  }
  
  /**
   * Remove a notify item
   */
  public function removeNotifyItem($id){
    return \Lobby\UI\Panel::removeNotifyItem("app_{$this->id}_$id");
  }
  
  public function u($path = null){
    return $path === null ? \Lobby::u() : $this->url . $path;
  }
  
  public function l($path, $text = "", $extra = ""){
    return \Lobby::l($this->url . $path, $text, $extra);
  }
  
  public function get($path){
    return \Lobby\FS::get($this->dir . $path);
  }
  
  public function write($path, $content, $type = "w"){
    return \Lobby\FS::write($this->dir . $path, $content, $type);
  }
  
  public function redirect($path){
    return Response::redirect(self::u($path));
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
