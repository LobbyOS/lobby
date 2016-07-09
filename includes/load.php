<?php
session_start();

/**
 * Define the Lobby Location
 * $docRoot would be set by /load.php
 */
define("L_DIR", str_replace("\\", "/", $docRoot));

try{
  /**
   * Autoload and initialize classes
   */
  $composer = require_once L_DIR . "/includes/src/vendor/autoload.php";
  
  /**
   * Get Lobby Defined Values
   */
  require_once L_DIR . "/includes/config.php";
  
  /**
   * Load Classed that Composer doesn't load by default
   */
  $composer->loadClass("Assets");
  $composer->loadClass("CSRF");
  $composer->loadClass("Lobby\\DB");
  $composer->loadClass("Lobby\\UI\\Themes");
  
  /**
   * Static Class Constructor
   * ------------------------
   * Call __constructStatic() on each classes with params for some classes
   */
  $loader = new ConstructStatic\Loader($composer);
  
  $loader->setClassParameters("Lobby\Apps", APPS_DIR);
  $loader->setClassParameters("Lobby\UI\Themes", THEMES_DIR);
  
  $loader->processLoadedClasses();
  
  /**
   * Set constants & Load Modules
   */
  require_once L_DIR . "/includes/extra.php";
  
  /**
   * These classes are not loaded by default by Composer
   */
  $loader->loadClass("Lobby\\Require");
}catch(\Exception $e){
  \Lobby::log(array("fatal", $e->getMessage()));
}

/**
 * Run not on CDN files serving
 */
if(!\Lobby::status("lobby.assets-serve")){
  /**
   * Init the page setup
   */
  require_once L_DIR . "/includes/init.php";
 
  /**
   * Is Lobby Installed ?
   */
  if(!\Lobby::$installed && !\Lobby::status("lobby.install")){
    \Response::redirect("/admin/install.php");
  }
}

\Hooks::doAction("init");
