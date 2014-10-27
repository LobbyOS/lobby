<?php
$App  = new App();
$apps = $App->getEnabledApps();
if(count($apps) == 0){
	ser("No Apps.", "You haven't enabled/installed any apps. <br/>Get Apps From <a href='". L_HOST ."/admin/appCenter.php'>App Center</a>");
}
$jsCode = "lobby.dash.data = ". getOption("dashItems") .";";
foreach($apps as $app){
	$App  	 	= new App($app);
	$data 	 	= $App->getInfo();
	$appImage 	= !isset($data['image']) || $data['image'] == "" ? L_HOST . "/includes/lib/core/img/blank.png" : APPS_URL . "/$app/{$data['image']}";
	$jsCode 	.= "lobby.dash.addTile('app', {'id' : '{$app}', 'img' : '{$appImage}', 'name' : '{$data['name']}'});";
}
echo "<script>$(function(){ $jsCode });</script>";
?>
<div class="tiles"></div>