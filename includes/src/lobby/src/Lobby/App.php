<?php
namespace Lobby;

use Lobby\Apps\Data;
use Lobby\Apps\Panel;
use Lobby\Apps\Router;
use Lobby\DB;
use Lobby\FS;
use Response;

class App {

  /**
   * @var FS Filesystem object with app's folder as base path
   */
  public $fs;

  /**
   * @var Router App's router object
   */
  public $router;

  /**
   * @var array App's manifest values
   */
  public $manifest = array();

  /**
   * @param array $appInfo Array containing object properties to be set
   */
  public function __construct(array $appInfo = array()){
    if(empty($appInfo))
      return null;

    $this->manifest = $appInfo;

    foreach($appInfo as $key => $value){
      $this->{$key} = $value;
    }

    $this->fs = new FSObj($this->dir);
    $this->data = new Data($this);
    $this->router = new Router($this);
    $this->panel = new Panel($this);

    $this->init();
  }

  /**
   * Initialize callback
   */
  public function init(){}

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
   * Get response of Asynchronous Request
   * @param string $handler Name of the AR handler
   */
  public function getARResponse($handler){
    $arResponse = $this->ar($handler);
    if($arResponse === "auto"){
      /**
       * Directory index
       */
      if(is_dir($this->fs->loc("src/ar/$handler")))
        $handler = "$handler/index";

      $arResponse = $this->inc("src/ar/$handler.php");
    }
    return $arResponse;
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

  /**
   * Handle Asynchronous Requests
   * @param  string $handler Handler file path
   * @return string          Response
   */
  public function ar($handler){
    return "auto";
  }

  /**
   * Callback on app install/update
   * @param string $newVersion The version to which the app is updated
   * @param string $oldVersion The version from which the app is updated
   */
  public function onUpdate($newVersion, $oldVersion = null){}

  /**
   * Write messages to log file
   */
  public function log($msg){
    \Lobby::log($msg, "app.". $this->id . ".log");
  }

}
