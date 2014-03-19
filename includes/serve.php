<?
require "load.php";
require "min-css.php";
require "js-shrinker.php";
$f=$_GET['file'];
if(preg_match("/\.css/",$f)){
 header("Content-type: text/css");
 $css=1;
}
if(preg_match("/\.js/",$f)){
 header("Content-type: application/x-javascript");
 $js=1;
}
function css_minfiy($s){
 $plugins = array("Variables"=>true,"ConvertFontWeight"=>true,"ConvertHslColors"=>true,"ConvertRgbColors"=>true,"ConvertNamedColors"=>false,"CompressColorValues"=>false,"CompressUnitValues"=>true,"CompressExpressionValues"=>true);
 $minifier = new CssMinifier($s,array(),$plugins);
 $result = $minifier->getMinified();
 return $result;
}
function js_minfiy($s){
 $j=new JSqueeze();
 $c=$j->squeeze($s,true,false);
 return $s;// $s for no minifciation & $c for minification
}
if(preg_match("/,/", $f)){
 $files=explode(",",$f);
}else{
 $files=array($f);
}
$content="";
$extraContent="";
foreach($files as $v){
 $v=str_replace(L_HOST."/", "", $v);
 if($v=="includes/js/jquery-ui.js" || $v=="includes/js/jquery.js"){
  $extraContent.=file_get_contents(L_ROOT.$v);
 }else{
  $content.=file_get_contents(L_ROOT.$v);
 }
 $to_replace=array(
  "<[host]>" => L_HOST,
  "<[app_uri]>" => APP_URI
 );
 foreach($to_replace as $k=>$val){
  $content=str_replace($k, $val, $content);
 }
}
if(isset($css)){
 $content=css_minfiy($content);
}
if(isset($js)){
 $content=js_minfiy($content);
}
echo $extraContent.$content;
?>
