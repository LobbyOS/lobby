<?
if(isset($_POST['id'])){
 $id=$_POST['id'];
 if($id!="" && getData("ledit", $id)){
  removeData("ledit", $id);
 }
}
?>
