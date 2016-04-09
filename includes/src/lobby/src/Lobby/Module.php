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
    $this->dir = $vars[0];
    $this->url = $vars[1];
    
    if($app !== null){
      $this->app = $app;
    }
    
    $this->init();
  }
  
  public function init(){}

}
