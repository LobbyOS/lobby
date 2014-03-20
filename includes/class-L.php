<?
class L {
 public $debug, $root, $host, $title = "";
 var $js = array();
 var $css = array();
 function __construct(){
  $this->root = L_ROOT;
  $docroot=substr($_SERVER['DOCUMENT_ROOT'], -1)=="/" ? substr_replace($_SERVER['DOCUMENT_ROOT'],"",-1) : $_SERVER['DOCUMENT_ROOT'];
  $host=str_replace($docroot, $_SERVER['HTTP_HOST'], substr_replace(L_ROOT,"",-1));
  $this->host = "http://".$host;
 }
 function debug($v=false){
  $this->debug = $v;
  if($v){
   ini_set("display_errors","on");
  }
 }
 function addScript($name, $url){
  $this->js[$name]=$url;
 }
 function addStyle($name, $url){
  $this->css[$name]=$url;
 }
 function setTitle($v){
  $this->title=$v;
 }
 function head($t){
  if(isset($t)){
   $this->title=$t;
  }elseif($this->title==""){
   $t="Lobby";
  }else{
   $this->title.=" - Lobby";
  }
  /*JS*/
  if(count($this->js)!=0){
   echo "<script async='async' src='".L_HOST."/includes/serve.php?file=".implode(",", $this->js)."'></script>";
  }
  /*CSS*/
  echo "<link async='async' href='".L_HOST."/includes/serve.php?file=".implode(",", $this->css)."' rel='stylesheet'/>";
  /*Title*/
  echo "<title>".$this->title."</title>";
 }
}
$LC = new L();
?>
