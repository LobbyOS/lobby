<?
session_start();
ini_set("display_errors", "on");
define("L_ROOT", realpath(dirname(str_replace("includes","",__FILE__)))."/");
require L_ROOT."includes/class-L.php";
require L_ROOT."includes/class-db.php";
require L_ROOT."includes/class-app.php";
require L_ROOT."includes/config.php";
require L_ROOT."includes/functions.php";
if(curFile()!="serve.php"){
 /* Extends */
 require L_ROOT."includes/class-home.php";
 /* Load Default Style For Home*/
 require L_ROOT."includes/loadHome.php";
 /* Is Lobby Installed ? */
 if(!$db->db && curFile()!="install.php"){
  redirect("{$LC->host}/admin/install.php");
 }
}
?>
