<?
include("includes/load.php");
$req="";
$L_OPT=array("page"=>"");
if(L_REQUEST_URI!="/"){
 $req=L_REQUEST_URI;
 $manifest=false;
 $reqpath=parse_url($req);
 $reqpath=$reqpath['path'];
 $reqParts=explode("/", $reqpath);
 if(file_exists($req)){
  include($req);
 }else if($reqParts[1]=="app" && isset($reqParts[2])){
  $AppName=$reqParts[2];
  $App=new App($AppName);
  $manifest=$App!=false ? $App->getInfo():false;
  $L_OPT['page']=!isset($reqParts[3]) || $reqParts[3]=="" ? "index":$reqParts[3];
  if(!file_exists($manifest['location']."/run.php") || $App->isEnabled()===false){
   ser();
  }
  define("CUR_APP", $manifest['location']);
  define("CUR_APP_URI", L_HOST."/app/".$AppName);
  $file=CUR_APP.$L_OPT['page'];
  if(file_exists($file)){
   $url=L_HOST."/contents/apps/".$AppName."/".$L_OPT['page'];
   $headers=get_headers($url);
   foreach($headers as $headerString){
    header($headerString);
   }
   include($file);
   exit;
  }
 }else{
  ser();
 }
}
?>
<html>
 <head>
  <?
  if(isset($manifest['pages'][$L_OPT['page']]['css'])){
   $csses=$manifest['pages'][$L_OPT['page']]['css'];
   foreach($csses as $k=>$v){
    $LC->addStyle("{$AppName}/$k", APP_URI."/{$AppName}/$v");
   }
  }
  if(isset($manifest['pages'][$L_OPT['page']]['js'])){
   $jsses=$manifest['pages'][$L_OPT['page']]['js'];
   foreach($jsses as $k=>$v){
    $LC->addScript("{$AppName}/$k", APP_URI."/{$AppName}/$v");
   }
  }
  if($req==""){
   $LC->addStyle("dashboard", L_HOST."/includes/css/dashboard.css");
   $LC->head("Dashboard");
  }else if(isset($L_OPT)){
   if(isset($manifest['pages'][$L_OPT['page']])){
    $LD->addTopItem("App > ".$manifest['name'], CUR_APP_URI, "left");
    $LC->head($manifest['pages'][$L_OPT['page']]['title']);
   }else{
    $LC->head($manifest['name']);
   }
  }
  ?>
 </head>
 <body>
  <?
  include("includes/ps/top.php");
  ?>
  <div class="workspace" id="<?
   if(isset($L_OPT)){
    echo $AppName;
   }
  ?>">
  <?
  if($req==""){
   include("includes/ps/main.php");
  }else if(isset($L_OPT)){
   include($manifest['location']."run.php");
  }
  ?>
  </div>
 </body>
</html>
