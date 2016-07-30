<?php
namespace Lobby;

use Response;
use Lobby\AppRouter;
use Lobby\DB;
use Lobby\FS;

class App {

  /**
   * Lobby\FS Object with App Dir as base
   */
  public $fs = null;
  
  /**
   * Klein router object
   */
  public $router = null;
  
  public function __construct(){
    $this->router = new AppRouter($this);
  }
  
  /**
   * @param array $appInfo Array containing object properties to be set
   */
  public function setAppInfo(array $appInfo){
    foreach($appInfo as $key => $value){
      $this->{$key} = $value;
    }
    $this->manifest = $appInfo;
    $this->fs = new FSObj($this->dir);
  }
  
  /**
   * @param string $page Path of requested page
   */
  public function getPageContent($page){
    /**
     * Set routes
     */
    foreach($this->routes() as $route => $callback)
      $this->router->route($route, $callback);
    
    $pageResponse = $this->router->dispatch();
    
    /**
     * If no routes are matched, ask $this->page() for response
     */
    if($pageResponse == null){
      $pageResponse = $this->page($page);
      if($pageResponse === "auto"){
        if($page === "/")
          $page = "/index";
        
        /**
         * Directory index
         */
        if(is_dir($this->fs->loc("src/page/$page")))
          $page = "$page/index";
        
        $pageResponse = $this->inc("src/page/$page.php");
      }
    }
    return $pageResponse;
  }
  
  /**
   * @param string $handler Name of the AJAX handler
   */
  public function getAJAXResponse($handler){
    $ajaxResponse = $this->ajax($handler);
    if($ajaxResponse === "auto"){
      /**
       * Directory index
       */
      if(is_dir($this->fs->loc("src/ajax/$handler")))
        $handler = "$handler/index";
      
      $ajaxResponse = $this->inc("src/ajax/$handler.php");
    }
    return $ajaxResponse;
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
  
  public function routes(){
    return array();
  }
  
  public function page($page){
    return "auto";
  }
  
  public function ajax($ajax){
    return "auto";
  }
  
  /**
   * Write messages to log file
   */
  public function log($msg){
    \Lobby::log($msg, "app.". $this->id . ".log");
  }
  
}
