<?
require "../includes/load.php";
if(isset($_POST['s7c8csw91'])){
 $s7c8csw91=$_POST['s7c8csw91'];
 $cx74e9c6a45=urldecode($_POST['cx74e9c6a45']);
 define("CUR_APP", APP_DIR."$s7c8csw91/");
 define("CUR_APP_URI", L_HOST."/app/$s7c8csw91");
 if($s7c8csw91=="" || $cx74e9c6a45==""){
  ser();
 }else{
  include(CUR_APP."$cx74e9c6a45");
 }
}
?>
