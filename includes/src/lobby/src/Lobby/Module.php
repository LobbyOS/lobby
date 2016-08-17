<?php
/**
 * Lobby\Module
 */
namespace Lobby;

use \Lobby\App;
use \Lobby\FS;

/**
 * A module's base class.
 * All modules extend from this class
 */
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
  
  /**
   * Set up properties of this object
   * @param string $vars Basic info about this module
   * @param Lobby\App $app If it's an App module, app's object is passed
   */
  public function __construct($vars, App $app = null){
    $this->id = $vars[0];
    $this->dir = $vars[1];
    $this->url = $vars[2];
    
    if($app !== null){
      $this->app = $app;
    }
    
    $this->init();
  }
  
  /**
   * Load a JS file from module's "js" directory
   * @param string $fileName Path to JS file
   */
  public function addScript($fileName){
    $filePath = FS::rel("{$this->dir}/js/$fileName");
    \Assets::js("{$this->id}-{$fileName}", $filePath);
  }
  
  /**
   * Load a stylesheet from module's "css" directory
   * @param string $fileName 
   * @return string 
   */
  public function addStyle($fileName){
    $filePath = FS::rel("{$this->dir}/css/$fileName");
    \Assets::css("{$this->id}-{$fileName}", $filePath);
  }
  
  /**
   * Initialize Module.
   * When module is loaded, this is called.
   * @return string 
   */
  public function init(){}

}
