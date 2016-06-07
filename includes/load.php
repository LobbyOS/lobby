<?php
session_start();

/**
 * Define the Lobby Location
 * $docRoot would be set by /load.php
 */
define("L_DIR", str_replace("\\", "/", $docRoot));

$_SERVER['ORIG_REQUEST_URI'] = $_SERVER['REQUEST_URI'];

/**
 * Make the request URL relative to the base URL of Lobby installation.
 * http://localhost/lobby will be changed to "/"
 * and http://lobby.local to "/"
 * ---------------------
 * We do this directly to $_SERVER['REQUEST_URI'] because, Klein (router)
 * obtains the value from it. Hence we keep the original value in ORIG_REQUEST_URI
 */
$lobbyBase = str_replace(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']), "", L_DIR);
$lobbyBase = substr($lobbyBase, 0) == "/" ? substr_replace($lobbyBase, "", 0) : $lobbyBase;

$_SERVER['REQUEST_URI'] = str_replace($lobbyBase, "", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], -1) == "/" && $_SERVER['REQUEST_URI'] != "/" ? substr_replace($_SERVER['REQUEST_URI'], "", -1) : $_SERVER['REQUEST_URI'];

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
  $composer->loadClass("Lobby\\DB");
  
  /**
   * Static Class Constructor
   * ------------------------
   * Call __constructStatic() on each classes with params for some classes
   */
  $loader = new ConstructStatic\Loader($composer);
  
  $loader->setClassParameters(\Lobby\UI\Themes::class, THEMES_DIR);
  $loader->setClassParameters(\Lobby\Apps::class, APPS_DIR);
  
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
    Response::redirect("/admin/install.php");
  }
}

\Lobby::doHook("init");
