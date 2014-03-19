<?
/* Get Full Web Page URL */
function curPageURL(){
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
define("L_HOST", $LC->host);
define("APP_URI", L_HOST."/contents/apps");
define("APP_DIR", L_ROOT."contents/apps/");
define("L_REQUEST_URI", str_replace(L_HOST, "", curPageURL()));
$GLOBALS['db']=new db();
$db=$GLOBALS['db'];
?>
