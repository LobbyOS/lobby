<?php
namespace Lobby;

use \Lobby\App;

class Module {

  /**
   * The full location to contents/modules/module
   */
  public $dir = "";
  
  /**
   * The HTTP URL to contents/modules/module
   */
  public $url = "";
  
  /**
   * If it's an App Module, add App instance
   */
  public $app;
  
  public function __construct($vars, App $app = null){
    $this->id = $vars[0];
    $this->dir = $vars[1];
    $this->url = $vars[2];
    
    if($app !== null){
      $this->app = $app;
    }
    
    $this->init();
  }
  
  public function addScript($fileName){
    $url = "{$this->url}/js/$fileName";
    \Assets::js("{$this->id}-{$fileName}", $url);
  }
  
  public function addStyle($fileName){
    $url = "{$this->url}/css/$fileName";
    \Assets::css("{$this->id}-{$fileName}", $url);
  }
  
  public function init(){}

}
