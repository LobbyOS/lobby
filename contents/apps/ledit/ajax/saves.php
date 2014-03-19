<h2>My Saves</h2>
<?
$saves=getData("ledit");
if(!$saves){
 echo "You haven't saved anything.";
}else{
 foreach($saves as $v){
  echo "<li><a href='".CUR_APP_URI."?id=".urlencode($v['name'])."'>".$v['name']."</a></li>";
 }
}
?>
