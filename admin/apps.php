<?include("../load.php");?>
<html>
 <head>
  <?$LC->head("App Manager");?>
 </head>
 <body>
  <?include("../includes/source/top.php");?>
  <div class="workspace">
   <div class="contents">
    <h2>App Manager</h2>
    <p>You can remove or disable installed apps using this page.<br/>You can Install Great Apps from <a href="<?echo L_HOST;?>/admin/appCenter.php">App Center</a>.</p>
    <?
    if(isset($_GET['action']) && isset($_GET['app'])){
     $action = $_GET['action'];
     $app	 = $_GET['app'];
     $App	 = new App($app);
     if(!$App->exists){
      	ser("Error", "I checked all over, but App Does Not Exist");
     }
     if($action=="disable"){
      	if($App->disableApp()){
       		sss("Disabled", "App has been disabled.");
      	}else{
       		ser("Error", "The App couldn't be disabled. Try again.", false);
      	}
     }else if($action=="remove"){
    ?>
      <h2>Confirm</h2>
      <p>Are you sure you want to remove the app <b><?echo $_GET['app'];?></b> ?</p><div clear></div>
      <a class="button" href="<?echo L_HOST;?>/admin/install-app.php?action=remove&id=<?echo $_GET['app'];?>">I'm Sure</a>
    <?
      	exit;
     }else if($action=="enable"){
      	if($App->enableApp()){
       		sss("Enabled", "App has been enabled.");
      	}else{
       		ser("Error", "The App couldn't be enabled. Try again.", false);
      	}
     }
    }
    $enabledApps = App::getEnabledApps();
    if(count($enabledApps) == 0){
    	ser("No Enabled Apps", "", false);
    }
    if(count($enabledApps) != 0){
    ?>
     <h3>Enabled Apps</h3>
     <table style="width: 100%;margin-top:5px">
     	<thead>
     		<tr>
       		<td>Name</td>
       		<td>Short Description</td>
       		<td>Actions</td>
      	</tr>
     	</thead>
     	<tbody>
      	<?
      	foreach($enabledApps as $app){
       		$App 		 = new App($app);
       		$data		 = $App->getInfo();
       		$appImage = !isset($data['image']) ? L_HOST."/includes/source/img/blank_app.png" : $data['image'];
      	?>
      		<tr>
        			<td><a href="<?php echo L_HOST.'/app/'.$app;?>"><?php echo $data['name'];?></a></td>
        			<td><?php echo$data['short_description'];?></td>
        			<td>
         			<a href="?action=disable&app=<?php echo $app;?>">Disable</a> |
         			<a href="?action=remove&app=<?php echo $app;?>">Remove</a>
        			</td>
       		</tr>
      	<?}?>
     	</tbody>
     </table>
    <?
    }
    $disabledApps = App::getDisabledApps();
    if(count($disabledApps) == 0){
     	ser("No Disabled Apps", "You haven't disabled any apps.", false);
    }
    if(count($disabledApps) != 0){
    ?>
     <h3>Disabled Apps</h3>
     <table>
     	<tbody>
      	<thead>
      		<tr>
       			<td>Name</td>
       			<td>Short Description</td>
       			<td>Actions</td>
      		</tr>
      	</thead>
      	<?
      	foreach($disabledApps as $app){
       		$App 		 = new App($app);
       		$data		 = $App->getInfo();
       		$appImage = !isset($data['image']) ? L_HOST."/includes/source/img/blank_app.png" : $data['image'];
      	?>
      		<tr>
        			<td><a href="<?echo L_HOST.'/app/'.$app;?>"><?echo$data['name'];?></a></td>
        			<td><?echo$data['short_description'];?></td>
        			<td>
         			<a href="?action=enable&app=<?echo$app;?>">Enable</a> |
         			<a href="?action=remove&app=<?echo$app;?>">Remove</a>
        			</td>
       		</tr>
      	<?}?>
     	</tbody>
     </table>
    <?}?>
    <style>
    .contents table{
    	table-layout:fixed;
    	max-width:500px;
    }
    </style>
   </div>
  </div>
 </body>
</html>