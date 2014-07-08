<?php include("../load.php");?>
<html>
 	<head>
  		<?php
  		$LC->addStyle("appC", L_HOST."/includes/source/css/appC.css");
  		$LC->head("App Manager");
  		?>
 	</head>
 	<body>
  		<?php include("../includes/source/top.php");?>
  		<div class="workspace">
   		<div class="contents">
    			<?php
    			if(isset($_GET['id']) && $_GET['id']!=""){
     				$appJSON = $LC->loadURL(L_SERVER."/appCenter.php", array(
      				"get" => "app",
      				"id"  => $_GET['id']
     				), "POST");
     				
     				if($appJSON == "false"){
      				ser("Error", "App With the given ID does not exist");
     				}
     				
     				$app 	 	 = json_decode($appJSON, true);
     				$app 	 	 = $app[$_GET['id']];
     				$appImage = isset($apps['image']) ? L_HOST."/includes/source/img/blank_app.png" : $app['image'];
    			?>
    				<h2><?php echo $app['name'];?></h2>
    				<div style="width:500px;"></div>
    				<div id="leftpane" style="float:left;margin-right:45px;display:inline-block;width:87px;">
     					<img src="<?echo $appImage;?>" height="120" width="120" />
     					<div clear></div>
     					<a href="<?php echo $app['appURL'];?>" target="_blank" class="button">App Page</a>
     					<div clear></div>
     					<?php
     					$App = new App($_GET['id']);
     					if(!$App->exists){
     					?>
      					<a href="<?php echo L_HOST;?>/admin/install-app.php?id=<?php echo $_GET['id'];?>" class="button">Install</a>
     					<?php
     					}else{
     					?>
      					<a href="<?php echo $App->getURL();?>" class="button">Open App</a>
     					<?php
     					}
     					?>
     					<style>#leftpane .button{width:100%;}</style>
    				</div>
    				<div style="display:inline-block;margin-top:-15px;">    
     					<h3>Version</h3>
     					<strong><?php echo $app['version'];?></strong>
     					<div>
     					Last Updated On <?php echo $app['updated'];?>
     					</div>
     					<h3>Description</h3>
     					<p style="max-width: 300px;">
     						<?php echo $app['description'];?>
     					</p>
     					<h3>Author</h3>
     					<a href="<?php echo $app['authorURL'];?>" target="_blank"><?php echo $app['authorName'];?></a>
    				</div>
    			<?php
    			}else{
    			?>
    				<h2>App Center</h2>
    				<p>Find Great New Apps</p>
    				<div clear></div>
    				<form method="GET" action="<?echo L_HOST?>/admin/appCenter.php" style="text-align: center;">
     					<input type="text" placeholder="Type an app name" name="q" style="width:450px;"/>
     					<button>Search</button>
    				</form>
    				<?
    				$appsJSON = $LC->loadURL(L_SERVER . "/appCenter.php", array(
     					"get" => "newApps"
    				), "POST");
    				
    				if($appsJSON == "false" || $appsJSON == ""){
     					ser("Nothing Found", "Nothing was found that matches your criteria. Sorry");
    				}
    				
    				$apps = json_decode($appsJSON, true);
    				if( !is_array($apps) ){
     					ser("Sorry", "The Lobby Server is experiencing some problems. Please Try again.");
    				}
    				
    				foreach($apps as $appID => $appArray){
     					$appImage = isset($appArray['image']) ? L_HOST."/includes/source/img/blank_app.png" : $appArray['image'];
    				?>
    					<div class="app">
     						<div class="left">
      						<a href="?id=<? echo $appID;?>"><img src="<?echo $appImage;?>" height="120" width="120"/></a>
      						<div clear></div>
      						<?
      						$App = new App($appID);
      						if(!$App->exists){
      						?>
      							<a href="<?echo L_HOST;?>/admin/install-app.php?id=<?echo$appID;?>" style="width:100%;" class="button">Install</a>
      						<?
      						}else{
      						?>
       							<a href="<?echo $App->getURL();?>" class="button" style="text-align:center;width:100%;">Open App</a>
      						<?
      						}
      						?>
     						</div>
     						<div class="right">
      						<a href="?id=<?echo$appID;?>"><div class="title"><?echo $appArray['name'];?></div></a>
      						<div class="description"><?echo $appArray['description'];?></div>
      						<table class="info">
       							<thead>
       								<tr>
       									<td>Version</td>
       									<td>By</td>
       									<td>Updated</td>
       									<td></td>
       								</tr>
       							</thead>
       							<tbody>
       								<tr>
       									<td><?php echo $appArray['version'];?></td>
       									<td><a href="<?echo $appArray['authorURL'];?>" target="_blank"><?echo $appArray['authorName'];?></a></td>
       									<td><?php echo date("j F Y", strtotime($appArray['updated']));?></td>
       									<td><a href="<?php echo$appArray['appURL'];?>" target="_blank">App Page</a></td>
       								</tr>
      							</tbody>
      						</table>
     						</div>
    					</div>
    				<?
    				}
    				?>
    			<?
    			}
    			?>
   		</div>
  		</div>
	</body>
</html>