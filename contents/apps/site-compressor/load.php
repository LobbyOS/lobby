<?
ini_set("display_errors", "on");

/* The full path of the SC's directory */
$siteRoot = realpath(dirname(__FILE__)."/");
include "$siteRoot/includes/class.siteCompressor.php";
$SC = new SiteCompressor($siteRoot);
?>