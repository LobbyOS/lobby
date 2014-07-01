<div class="apps">
	<?
 	$App  = new App();
 	$apps = $App->getEnabledApps();
 	if(count($apps)==0){
  		ser("No Apps.","You haven't enabled/installed any apps. <br/>Get Apps From <a href='".L_HOST."/admin/appCenter.php'>App Center</a>");
 	}
 	foreach($apps as $app){
  		$App  	 = new App($app);
  		$data 	 = $App->getInfo();
  		$appImage = !isset($data['image']) || $data['image']=="" ? L_HOST . "/includes/source/img/blank_app.png" : APPS_URL . "/$app/{$data['image']}";
 	?>
  		<div class="app" data-cols="1" data-rows="1">
   		<div class="overlay"></div>
   		<a href="<?echo L_HOST . "/app/{$app}";?>">
    			<div class="inner">
     				<div class="image">
      				<img src="<?echo$appImage;?>" height="100%" width="100%"/>
     				</div>
     				<div class="title" <?if(!isset($data['image']) || $data['image']==""){echo 'style="color:black;"';}?>><?echo $data['name'];?></div>
    			</div>
   		</a>
  		</div>
 	<?
 	}
 	?>
</div>