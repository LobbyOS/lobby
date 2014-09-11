<?php
session_start();
/* Define the Root */
$docRoot = isset($docRoot) ? $docRoot : realpath( dirname( dirname(__FILE__) ) );
define("L_ROOT", $docRoot);

require_once L_ROOT . "/includes/classes/core.php"; /* the Core Class */
require_once L_ROOT . "/includes/classes/db.php"; /* The Database Class */
require_once L_ROOT . "/includes/extraDefinitions.php"; /* Define extra variables or constants */
require_once L_ROOT . "/includes/classes/apps.php"; /* The App Class */
require_once L_ROOT . "/includes/functions.php"; /* Functions that are a shortcut to class functions */

require_once L_ROOT . "/includes/classes/Helpers.php"; /* Helping Functions that provides additional functionality */

/* Load System Installation configuration */
require_once L_ROOT . "/includes/config.php";

if(Helpers::curPage() != "serve.php"){
 	/* Extends */
 	require L_ROOT . "/includes/classes/home.php";
 
 	/* Load Default Style For Home*/
 	require L_ROOT . "/includes/loadHome.php";
 
 	/* Is Lobby Installed ? */
 	if(!$db->db && Helpers::curPage() != "install.php"){
  		$LC->redirect("{$LC->host}/admin/install.php");
 	}
}
?>