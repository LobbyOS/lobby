<?
/* Check For New Versions (Apps & Lobby) */
if(!isset($_SESSION['checkedForLatestVersion'])){
 $App=new App();
 $response=load(L_SERVER."/core/latestVersion.php", array(
  "apps" => implode(",", $App->getApps())
 ), "POST");
 if($response){
  $response=json_decode($response, true);
  saveOption("lobby_latest_version", $response['version']);
  saveOption("lobby_latest_version_release", $response['released']);
  if(isset($response['apps']) && count($response['apps'])!=0){
   $AppUpdates=array();
   foreach($response['apps'] as $k=>$v){
    $App=new App($k);
    $AppInfo=$App->getInfo();
    if($AppInfo['version']!=$v){
     $AppUpdates[$k]=$v;
    }
   }
   saveOption("app_updates",json_encode($AppUpdates));
  }
 }
 $_SESSION['checkedForLatestVersion']=1;
}
/* Default Styles */
$LC->addStyle("main", L_HOST."/includes/css/main.css");
if(curFile()!="admin/install.php"){
 /* Styles */
 $LC->addStyle("jqueryui", L_HOST."/includes/css/jquery-ui.css"); // jQuery UI
 $LC->addStyle("home", L_HOST."/includes/css/home.css");
 $LC->addStyle("panels", L_HOST."/includes/css/panels.css");
 
 /* Scripts */
 $LC->addScript("jquery", L_HOST."/includes/js/jquery.js");
 $LC->addScript("jqueryui", L_HOST."/includes/js/jquery-ui.js"); // jQuery UI
 $LC->addScript("main", L_HOST."/includes/js/main.js");
 $LC->addScript("superfish", L_HOST."/includes/js/superfish.js");
 $LC->addScript("home", L_HOST."/includes/js/home.js");
 
 /* Design */
 /*Left Menu*/
  $LD->addTopItem("Home", L_HOST, "left");
  $LD->addTopItem("Admin", array(
   "App Manager" => L_HOST."/admin/apps.php",
   "App Center" => L_HOST."/admin/appCenter.php",
   "About" => L_HOST."/admin/about.php"
  ), "left");
  
 /*Right Menu*/
  $LD->addTopItem("<span id='net' title='Online'></span>", array(), "right");
  $AppUpdates=json_decode(getOption("app_updates"), true);
  if((isset($AppUpdates) && count($AppUpdates)!=0) || (getOption("lobby_latest_version") && getOption("lobby_version")!=getOption("lobby_latest_version"))){
   $LD->addTopItem("<a href='".L_HOST."/admin/upgrade.php'><span id='upgrade' title='An Update Is Available'></span></a>", array(), "right");
  }
}
?>
