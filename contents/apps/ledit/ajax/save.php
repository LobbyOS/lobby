<?
if(isset($_POST['text'])){
 $t=$_POST['text'];
 if($t!=""){
  $name=isset($_POST['name']) && $_POST['name']!="" ? $_POST['name']:date("Y-m-d H:i:s");
  saveData("ledit", $name, $t);
 }
}
?>
