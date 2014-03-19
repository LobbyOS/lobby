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
  $App=new App($reqParts[2]);
  $manifest=$App!=false ? $App->getInfo():false;
  $L_OPT['page']=!isset($reqParts[3]) || $reqParts[3]=="" ? "index":$reqParts[3];
  if(!file_exists($manifest['location']."/run.php") || $App->isEnabled()===false){
   ser();
  }
  define("CUR_APP", $manifest['location']);
  define("CUR_APP_URI", L_HOST."/app/".$reqParts[2]);
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
    $LC->addStyle("{$reqParts[2]}/$k", APP_URI."/{$reqParts[2]}/$v");
   }
  }
  if(isset($manifest['pages'][$L_OPT['page']]['js'])){
   $jsses=$manifest['pages'][$L_OPT['page']]['js'];
   foreach($jsses as $k=>$v){
    $LC->addScript("{$reqParts[2]}/$k", APP_URI."/{$reqParts[2]}/$v");
   }
  }
  if($req==""){
   $LC->addStyle("dashboard", L_HOST."/includes/css/dashboard.css");
   $LC->head("Dashboard");
  }else if(isset($L_OPT)){
   if(isset($manifest['pages'][$L_OPT['page']])){
    $LD->addTopItem("<span> &gt; </span><a style='display:inline-block;vertical-align:middle;' href='".CUR_APP_URI."'>".$manifest['name']."</a>", array(), "left");
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
    echo $reqParts[2];
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
