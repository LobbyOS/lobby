<?php
include "../load.php";
require "$docRoot/admin/handleUpgrade.php";
?>
<!DOCTYPE html>
<html>
	<head>
 		<?php $LC->head("Upgrade");?>
	</head>
	<body>
 		<?php include "$docRoot/includes/lib/core/php/top.php";?>
		<?php include "$docRoot/admin/sidebar.php";?>
 		<div class="workspace">
 			<div class="content" style="min-width: 600px;">
	  			<h1>
	  				<center>Upgrade</center>
	  			</h1>
	  			<a class='button' href='checkReleases.php'>Check For New Releases</a>
	  			<?php
	  			$AppUpdates = json_decode(getOption("app_updates"), true);
	  			if( isset($_POST['action']) && $_POST['action'] == "upgradeApps" ){
					foreach($AppUpdates as $appID => $neverMindThisVariable){
	    				if( isset($_POST[$appID]) ){
	     					if( Upgrade::app($appID) ){
								unset($AppUpdates[$appID]);
								sss("Updated", "The app {$appID} was updated successfully.");
	     					}
	    				}
					}
					saveOption("app_updates", json_encode($AppUpdates));
					$AppUpdates = json_decode(getOption("app_updates"), true);
	  			}
	  			if(isset($AppUpdates) && count($AppUpdates)!=0){
	  			?>
	  				<h1>
	  					<center>Upgrade Apps</center>
	  				</h1>
	  				<p>App updates are available. You can choose if you want to update the following apps.</p>
	  				<form method="POST">
						<?php
						foreach($AppUpdates as $k=>$v){
	    					$App = new App($k);
	    					$AppInfo = $App->getInfo();
	    					echo '<label style="margin:10px;display:block;font-size: 14px;"><input style="vertical-align:top;display:inline-block;" checked="checked" type="checkbox" name="'.$k.'" /><span style="vertical-align:middle;display:inline-block;margin-left:5px;">'.$AppInfo['name'].' | Version '.$AppInfo['version'].'<br/>To '.$v.'</span></label>';
						}
						?>
						<input type="hidden" name="action" value="upgradeApps"/>
						<button>Upgrade Apps</button>
	  				</form>
	  			<?php
	  			}
	  			if(getOption("lobby_version") == getOption("lobby_latest_version")){
					sss("Latest Version", "You are using the latest version of Lobby. There are no new releases yet.");
	  			} else {
	  			?>
	  				<p>
						Welcome To The Lobby upgrade Page. A latest version is available for you to upgrade.
					</p>
	  				<blockquote>
						Latest Version is <?php echo getOption("lobby_latest_version");?>
						released on <?php echo date( "jS F Y", strtotime(getOption("lobby_latest_version_release")) );?>
	  				</blockquote>
	  				<p style="margin-bottom: 10px;">
						Lobby will automatically download the latest version and install. In case something happens, Lobby will not be accessible anymore. So backup your databases and Lobby installation before you do anything.
						<div clear></div>
						<a class="button" href="backupDatabase.php">Export Lobby Database</a>
						<?php
						if(!is_writable(L_ROOT)){
						?>
							<div clear></div>
							All you have to do is change the permission of 
								<blockquote><?php echo L_ROOT;?></blockquote>
							to read and write (777).
						<?php
						}
						?>
	  				</p>
					<?php
					if( is_writable(L_ROOT) ){
						sss("Permissions Are Correct", "Looks like the permission of Lobby directory is set to read & write. You may now proceed to the <a href='".L_HOST."/admin/upgrade.php?step=1'>next step</a>.");
					}
					?>
					<div clear style="margin-top: 10px;"></div>
					<a href="<?php echo L_HOST?>/admin/upgrade.php?step=1" class="button">Next Step</a>
	  			<?php
	  			}
	  			if(isset($_GET['step']) && $_GET['step'] != ""){
					$step=$_GET['step'];
					if($step == 1){
	    				if(!is_writable(L_ROOT)){
	     					ser("Error", "<b>".L_ROOT."/</b> is not writable.");
	    				}
	  			?>
						<p>
	    					Looks like everything is ok. Hope you backed up Lobby installation & Database.
	    					<div clear></div>
	    					You can upgrade now.
						</p>
						<div clear></div>
						<a href="<?php echo L_HOST?>/admin/upgrade.php?step=2" class="button">Upgrade</a>
	  			<?php
					}elseif($step == 2){
	    				Upgrade::software();
					}
				}
	  			?>
			</div>
 		</div>
	</body>
</html>