<?
/* Define the HOST of installation */
define("L_HOST", $LC->host);

define("APPS_URL", L_HOST . "/contents/apps");
define("APPS_DIR", L_ROOT . "/contents/apps");
$GLOBALS['db'] = new db();
$db = $GLOBALS['db'];
?>