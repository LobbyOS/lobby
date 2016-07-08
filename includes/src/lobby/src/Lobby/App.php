<?php
namespace Lobby;

use Response;
use Lobby\DB;
use Lobby\FS;

class App {

  /**
   * Lobby\FS Object with App Dir as base
   */
  public $fs = null;
  
  public function setTheVars(array $array){
    foreach($array as $key => $value){
      $this->{$key} = $value;
    }
    $this->manifest = $array;
    $this->fs = new FSObj($this->dir);
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
   * Save data
   */
  public function saveData($key, $value){
    return DB::saveData($this->id, $key, $value);
  }
  
  /**
   * Get JSON decoded array from a value of App's Data Storage
   */
  public function getJSONData($key = ""){
    $data = $this->getData($key, false);
    $data = json_decode($data, true);
    return is_array($data) ? $data : array();
  }
  
  /**
   * Save JSON as a value of App's Data Storage
   * To remove an item, set the value of it to (bool) FALSE
   */
  public function saveJSONData($key, $values){
    $data = $this->getJSONData($key);
    
    $new = array_replace_recursive($data, $values);
    foreach($values as $k => $v){
      if($v === false){
        unset($new[$k]);
      }
    }
    $new = json_encode($new);
    $this->saveData($key, $new);
    return true;
  }
  
  public function removeData($key){
    return DB::removeData($this->id, $key);
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
  
  public function u($path = null, $src = false){
    $path = ltrim($path, "/");
    return $path === null ? \Lobby::u() : ($src ? $this->srcURL : $this->url) . "/$path";
  }
  
  public function l($path, $text = "", $extra = ""){
    return \Lobby::l($this->url . $path, $text, $extra);
  }
  
  public function get($path){
    return $this->fs->get($path);
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
