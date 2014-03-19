<?
session_start();
session_destroy();
require("../includes/load.php");
header("Location: ".L_HOST."/admin/upgrade.php");
?>
