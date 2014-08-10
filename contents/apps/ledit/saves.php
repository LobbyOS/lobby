<h2 style="margin-top: -10px;">My Saves</h2>
<?php
$saves = getData("ledit");
if( !$saves ){
 	echo "You haven't saved anything.";
}else{
 	foreach($saves as $save){
 		$url = APP_URL . "?id=" . urlencode($save['name']);
  		echo "<li><a href='{$url}'>" . $save['name'] . "</a></li>";
 	}
}
?>