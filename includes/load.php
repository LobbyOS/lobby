<?
session_start();
require $_SERVER['DOCUMENT_ROOT']."/includes/class-L.php";
require $LC->root."includes/class-db.php";
require $LC->root."includes/class-app.php";
require $LC->root."includes/config.php";
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
