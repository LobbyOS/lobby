<?
header("Content-type:text/css");
require "load.php";
$f=$_GET['f'];
if($f!=""){
 $files=explode(",",$f);
 foreach($files as $v){
  if($v!=""){
   if(preg_match("/includes\./", $v)){
    $v=str_replace("includes.","",$v);
    echo file_get_contents(L_ROOT."includes/css/".$v.".css");
   }
  }
 }
}
?>
