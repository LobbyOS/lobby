<?php
session_start();
/* Define the Root */
$docRoot = isset($docRoot) ? $docRoot : realpath( dirname( dirname(__FILE__) ) );
define("L_ROOT", $docRoot);

/* Make the request URL relative to the base URL of Lobby installation. http://localhost/lobby will be changed to "/" and http://lobby.local to "/" */
$lobbyBase = str_replace($_SERVER['DOCUMENT_ROOT'], "", $docRoot);
$_SERVER['REQUEST_URI'] = str_replace($lobbyBase, "", $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], -1) == "/" && $_SERVER['REQUEST_URI'] != "/" ? substr_replace($_SERVER['REQUEST_URI'], "", -1) : $_SERVER['REQUEST_URI'];

require_once L_ROOT . "/includes/classes/core.php"; /* the Core Class */
require_once L_ROOT . "/includes/classes/db.php"; /* The Database Class */
require_once L_ROOT . "/includes/extraDefinitions.php"; /* Define extra variables or constants */
require_once L_ROOT . "/includes/classes/apps.php"; /* The App Class */
require_once L_ROOT . "/includes/functions.php"; /* Functions that are a shortcut to class functions */

require_once L_ROOT . "/includes/classes/Helpers.php"; /* Helping Functions that provides additional functionality */

/* Load System Installation configuration */
require_once L_ROOT . "/includes/config.php";

if(Helpers::curPage() != "/includes/serve.php"){
 	/* Extends */
 	require L_ROOT . "/includes/classes/home.php";
 
 	/* Load Default Style For Home*/
 	require L_ROOT . "/includes/loadHome.php";
 
 	/* Is Lobby Installed ? */
 	if(!$db->db && Helpers::curPage() != "/admin/install.php"){
  		$LC->redirect("{$LC->host}/admin/install.php");
 	}
}
?>