<?php include "../load.php";?>
<html>
	<head>
		<?php $LC->head("App Manager");?>
	</head>
	<body>
		<?php include "$docRoot/includes/source/top.php";?>
		<?php include "$docRoot/admin/sidebar.php";?>
		<div class="workspace">
			<div class="contents">
				<h2>App Manager</h2>
				<p>You can remove or disable installed apps using this page. You can Find and Install More Apps from <a href="<?php echo L_HOST;?>/admin/appCenter.php">App Center</a>.</p>
				<?php
				if(isset($_GET['action']) && isset($_GET['app'])){
					$action = $_GET['action'];
					$app	= $_GET['app'];
					$App	= new App($app);
					if( !$App->exists ){
						ser("Error", "I checked all over, but App does not Exist");
					}
					if($action == "disable"){
						if($App->disableApp()){
							sss("Disabled", "The App <strong>$app</strong> has been disabled.");
						}else{
							ser("Error", "The App <strong>$app</strong> couldn't be disabled. Try again.", false);
						}
					}else if($action=="remove"){
				?>
						<h2>Confirm</h2>
						<p>Are you sure you want to remove the app <b><?php echo $app;?></b> ?</p>
						<div clear></div>
						<a class="button green" href="<?php echo L_HOST ."/admin/install-app.php?action=remove&id={$app}";?>">Yes, I'm Sure</a>
						<a class="button red" href="<?php echo L_HOST ."/admin/apps.php";?>">No, I'm Not</a>
				<?php
						exit;
					}else if($action=="enable"){
						if($App->enableApp()){
							sss("Enabled", "App has been enabled.");
						}else{
							ser("Error", "The App couldn't be enabled. Try again.", false);
						}
					}
				}
				$Apps = new App();
				$Apps = $Apps->getApps();
    
				if(count($Apps) == 0){
					ser("No Enabled Apps", "", false);
				}
				if(count($Apps) != 0){
				?>
					<table style="width: 100%;margin-top:5px">
						<thead>
							<tr>
								<td>Name</td>
								<td>Version</td>
								<td>Description</td>
								<td>Actions</td>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($Apps as $app){
								$App	  = new App($app);
								$data	  = $App->getInfo();
								$appImage = !isset($data['image']) ? L_HOST."/includes/source/img/blank_app.png" : $data['image'];
								$enabled  = $App->isEnabled();
							?>
								<tr <?php if(!$enabled){ echo 'style="background:#DDD;"'; } ?>>
									<td><a href="<?php echo L_HOST.'/app/'.$app;?>"><?php echo $data['name'];?></a></td>
									<td><?php echo $data['version'];?></td>
									<td><?php echo $data['short_description'];?></td>
									<td style="text-align:center;">
										<?php
										if( $enabled ){
											echo '<a class="button" href="?action=disable&app='. $app .'">Disable</a>';
										}else{
											echo '<a class="button" href="?action=enable&app='. $app .'">Enable</a>';
										}
										?>
										<a class="button red" href="?action=remove&app=<?php echo $app;?>">Remove</a>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
				}
				?>
			</div>
		</div>
	</body>
</html>