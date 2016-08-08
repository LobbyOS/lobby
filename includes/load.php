<?php
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
   * Load Classes that has __constructStatic()
   */
  $composer->loadClass("Assets");
  $composer->loadClass("CSRF");
  $composer->loadClass("Lobby");
  $composer->loadClass("Request");
  $composer->loadClass("Response");
  $composer->loadClass("Lobby\\FS");
  $composer->loadClass("Lobby\\DB");
  $composer->loadClass("Lobby\\Apps");
  $composer->loadClass("Lobby\\Modules");
  $composer->loadClass("Lobby\\Router");
  $composer->loadClass("Lobby\\Time");
  $composer->loadClass("Lobby\\UI\\Themes");
  
  /**
   * Static Class Constructor
   * ------------------------
   * Call __constructStatic() on each classes with params for some classes
   */
  $loader = new ConstructStatic\Loader($composer);
  
  $loader->setClassParameters("Lobby\\Apps", array(APPS_DIR, APPS_URL));
  $loader->setClassParameters("Lobby\UI\Themes", array(THEMES_DIR, THEMES_URL));
  
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
