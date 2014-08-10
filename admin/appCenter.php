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
     				$appJSON = $LC->loadURL(L_SERVER."/appCenter", array(
						"get" => "app",
						"id"  => $_GET['id']
     				), "POST");
     				if($appJSON == "false"){
						ser("Error", "App With the given ID does not exist");
     				}
     				
     				$app 	  = json_decode($appJSON, true);
     				$app 	  = $app[$_GET['id']];
     				$appImage = isset($app['image']) ? $app['image'] : L_HOST . "/includes/source/img/blank_app.png";
    			?>
    				<h1><?php echo $app['name'];?></h1>
    				<p style="margin-bottom:15px;margin-top:-5px;"><?php echo $app['shortDescription'];?></p>
    				<div id="leftpane" style="float:left;margin-right:40px;display:inline-block;width:90px;text-align:center;">
     					<img src="<?php echo $appImage;?>" height="120" width="120" />
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
							<a href="<?php echo $App->getURL();?>" class="button green">Open App</a>
     					<?php
     					}
     					?>
     					<style>#leftpane .button{width:100%;}</style>
    				</div>
    				<div style="display:inline-block;">
     					<table>
     						<thead>
     							<tr>
     								<td>Version</td>
     								<td>Author</td>
     								<td>Last Updated</td>
     							</tr>
     						</tbody>
     						<tbody>
     							<tr>
     								<td><?php echo $app['version'];?></td>
     								<td><a href="<?php echo $app['authorURL'];?>" target="_blank"><?php echo $app['authorName'];?></a></td>
     								<td><?php echo date( "l, jS \of F Y", strtotime($app['updated']) );?></td>
     							</tr>
     						</tbody>
     					</table>
     					<h2>Description</h2>
     					<p style="max-width: 500px;">
     						<?php echo $app['description'];?>
     					</p>
					</div>
					<?php
					}else{
					?>
						<h2>App Center</h2>
						<p>Find Great New Apps</p>
						<div clear></div>
						<form method="GET" action="<?php echo L_HOST?>/admin/appCenter.php">
							<input type="text" placeholder="Type an app name" name="q" style="width:450px;"/>
							<button>Search</button>
						</form>
						<?php
						$appsJSON = $LC->loadURL(L_SERVER . "/appCenter", array(
							"get" => "newApps"
						), "POST");
    				
						if($appsJSON == "false" || $appsJSON == ""){
							ser("Nothing Found", "Nothing was found that matches your criteria. Sorry");
						}
    				
						$apps = json_decode($appsJSON, true);
						if( !is_array($apps) ){
							$LC->log("Lobby Server Replied : {$appsJSON}");
							ser("Sorry", "The Lobby Server is experiencing some problems. Please Try again.");
						}
    				
						foreach($apps as $appID => $appArray){
							$appImage = isset($appArray['image']) ? L_HOST."/includes/source/img/blank_app.png" : $appArray['image'];
						?>
							<div class="app">
								<div class="left">
								<a href="?id=<?php echo $appID;?>">
									<img src="<?php echo $appImage;?>" height="120" width="120"/>
								</a>
								<div clear></div>
								<?php
								$App = new App($appID);
								if(!$App->exists){
								?>
									<a href="<?php echo L_HOST;?>/admin/install-app.php?id=<?php echo$appID;?>" style="display: block;" class="button">Install</a>
								<?php
								}else{
								?>
									<a href="<?php echo $App->getURL();?>" class="button" style="display: block;">Open App</a>
								<?php
								}
								?>
								</div>
								<div class="right">
								<div class="title">
									<a href="?id=<?php echo $appID;?>"><?php echo $appArray['name'];?></a>
								</div>
								<div class="description"><?php echo $appArray['shortDescription'];?></div>
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
											<td><a href="<?php echo $appArray['authorURL'];?>" target="_blank"><?php echo $appArray['authorName'];?></a></td>
											<td><?php echo date("j F Y", strtotime($appArray['updated']));?></td>
											<td><a href="<?php echo$appArray['appURL'];?>" target="_blank">App Page</a></td>
										</tr>
									</tbody>
								</table>
     						</div>
    					</div>
    				<?php
    				}
    			}
    			?>
			</div>
  		</div>
	</body>
</html>