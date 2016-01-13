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

require_once L_DIR . "/includes/src/Helpers.php";
require_once L_DIR . "/includes/src/FileSystem.php"; // The FileSystem Class

require_once L_DIR . "/includes/src/Lobby.php"; /* The Core */
require_once L_DIR . "/includes/src/Database.php"; /* The Database Class */
require_once L_DIR . "/includes/src/Query.php"; /* The Database Class */
require_once L_DIR . "/includes/src/Apps.php"; /* The App Class */
require_once L_DIR . "/includes/src/Router.php"; /* The Router Class */
require_once L_DIR . "/includes/src/Server.php"; /* The File System Class */

require_once L_DIR . "/includes/functions.php"; /* Non class functions */
require_once L_DIR . "/includes/extra.php"; /* Define extra variables or constants */

/**
 * Timezone
 */
$tz = getOption("lobby_timezone");
if($tz){
  date_default_timezone_set($tz);
  $sql = \Lobby\DB::$dbh->prepare("SET time_zone = ?;");
  $sql->execute(array('Europe/Helsinki'));
}

/**
 * The UI Classes
 */
require_once L_DIR . "/includes/src/ui/Themes.php";

/**
 * Run not on CDN files serving
 */
if(!\Lobby::status("lobby.serve")){
  /**
   * Init the page setup
   */
  require_once L_DIR . "/includes/init.php";
 
  /**
   * Is Lobby Installed ?
   */
  if(!\Lobby::$installed && !\Lobby::status("lobby.install")){
    \Lobby::redirect("/admin/install.php");
  }
}

\Lobby::doHook("init");
