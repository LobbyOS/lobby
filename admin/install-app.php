<?include("../includes/load.php");?>
<!DOCTYPE html>
<html><head>
 <?$LC->head("Install App");?>
</head><body>
 <?include("../includes/ps/top.php");?>
 <div class="workspace">
  <div class="contents">
   <?
   if(!isset($_GET['id']) || $_GET['id']==""){
    ser("Error", "No App Was mentioned. Try <a href='appCenter.php'>App Center</a>");
   }
   if(isset($_GET['action']) && $_GET['action']=="enable"){
    $App=new App($_GET['id']);
    if(!$App->exists){
     ser("Error", "App is not installed");
    }
    $App->enableApp();
    sss("Enabled", "The App <b>{$_GET['id']}</b> is enabled. The author says thanks.");
    exit;
   }
   ?>
   <h2>Install</h2>
   <?
   if(isset($_GET['id']) && $_GET['id']!=""){
    $appsURI=load("http://lobby.host/core/appCenter.php", array(
     "get" => "app",
     "id" => $_GET['id']
    ), "POST");
    if($appsURI=="false"){
     ser("Error", "App With the give Id does not exist. The authro may have deleted the app.");
    }
    $apps=json_decode($appsURI, true);
    $apps=$apps[$_GET['id']];
   ?>
   <p>Downloading & Installing <b><?echo $apps['name'];?></b>.... Do NOT close this window.</p>
   <?
   require("handleUpgrade.php");
   if(appUpgrade($_GET['id'])){
    sss("Installed", "The app has been installed. All you have to do is <a href='".L_HOST."/admin/install-app.php?action=enable&id=".$_GET['id']."'>enable the app</a>.");
   }
   ?>
   <?}?>
  </div>
 <div>
</body></html>
