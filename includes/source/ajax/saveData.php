<?
require "../../load.php";
if(isset($_POST['appId']) && isset($_POST['key']) && isset($_POST['value'])){
 	$app = $_POST['appId'];
 	$key = $_POST['key'];
 	$val = $_POST['value'];
 	if(!saveData($app, $key, $val)){
  		echo "bad";
 	}else{
  		echo "good";
 	}
}else{
 	echo "fieldsMissing";
}
?>