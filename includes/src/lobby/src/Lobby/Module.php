<?php
namespace Lobby;

use \Lobby\App;
use \Lobby\FS;

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
    $filePath = FS::rel("{$this->dir}/js/$fileName");
    \Assets::js("{$this->id}-{$fileName}", $filePath);
  }
  
  public function addStyle($fileName){
    $filePath = FS::rel("{$this->dir}/js/$fileName");
    \Assets::css("{$this->id}-{$fileName}", $filePath);
  }
  
  public function init(){}

}
