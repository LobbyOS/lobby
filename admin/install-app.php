<?php
require "../load.php";
require L_ROOT . "/admin/handleUpgrade.php";
?>
<!DOCTYPE html>
<html>
	<head>
 		<?php $LC->head("Install App");?>
	</head>
	<body>
 		<?php
 		include L_ROOT . "/includes/source/top.php";
 		?>
 		<div class="workspace">
  			<div class="contents">
   			<?php
   			if(!isset($_GET['id']) || $_GET['id']==""){
    				ser("Error", "No App Was mentioned. Try <a href='appCenter.php'>App Center</a>");
   			}
   			if(isset($_GET['action']) && $_GET['action']=="enable"){
    				$App = new App($_GET['id']);
    				if(!$App->exists){
     					ser("Error", "App is not installed");
    				}
    				$App->enableApp();
    				sss("Enabled", "The App <b>{$_GET['id']}</b> is enabled. The author says thanks.<br/><div clear></div><center><a href='".$App->getURL()."'>Open App</a></center>");
    				exit;
   			}
   			if(isset($_GET['action']) && $_GET['action'] == "remove"){
    				$App = new App($_GET['id']);
    				if(!$App->exists){
     					ser("Error", "App is not installed");
    				}
    				$App->removeApp();
    				sss("Removed", "The App <b>{$_GET['id']}</b> was successfully removed.");
    				exit;
   			}
   			?>
   			<h2>Install</h2>
   			<?php
   			if(isset($_GET['id']) && $_GET['id']!=""){
    				$appsURI=$LC->loadURL(L_SERVER."/appCenter", array(
     					"get" => "app",
     					"id" => $_GET['id']
    				), "POST");
    				if($appsURI=="false"){
     					ser("Error", "App With the give Id does not exist. The author may have deleted the app.");
    				}
    				$apps=json_decode($appsURI, true);
    				$apps=$apps[$_GET['id']];
   			?>
   				<p>
   					Downloading & Installing <b><?php echo $apps['name'];?></b>.... Do NOT close this window.
   				</p>
   				<?php
   				if( Upgrade::app($_GET['id']) ){
    					sss("Installed", "The app has been installed. All you have to do is <a href='".L_HOST."/admin/install-app.php?action=enable&id=".$_GET['id']."'>enable the app</a>.");
   				}
   				?>
   			<?php }?>
			</div>
 		<div>
	</body>
</html>