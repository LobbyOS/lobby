<?
function getOption($key){
 if(!$GLOBALS['db']->db){
  return false;
 }else{
  return $GLOBALS['db']->getOption($key);
 }
}
function saveOption($key, $value){
 if(!$GLOBALS['db']->db){
  return false;
 }else{
  return $GLOBALS['db']->saveOption($key, $value);
 }
}
function curFile(){
 $parts=explode("/",$_SERVER['SCRIPT_FILENAME']);
 return $parts[count($parts)-1];
}
function ser($t="", $d=""){
 if($t==''){
  header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
  include(L_ROOT."includes/ps/error.php");
  exit;
 }else{
  $er="<h2 style='color:red;'>$t</h2>";
  if($d!=''){
   $er.="<span style='color:red;'>$d</span>";
  }
 }
 echo $er;
 exit;
}
function sss($t,$d){
 if($t==''){
  $s="<h2 style='color:green;'>Success</h2>";
 }else{
  $s="<h2 style='color:green;'>$t</h2>";
 }
 if($d!=''){
  $s.="<span style='color:green;'>$d</span>";
 }
 echo $s;
}
function uniqueStr($length){
 $str="";
 $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
 $size=strlen($chars);
 for($i=0;$i<$length;$i++){
  $str.=$chars[rand(0,$size-1)];
 }
 return$str;
}
function redirect($url){
 header("Location: $url");
 exit;
}
function load($url, $params=array(), $type="GET"){
 $ch = curl_init();
 if(count($params)!=0){
  $fields_string="";
  foreach($params as $key=>$value){
   $fields_string .= $key.'='.$value.'&';
  }
  rtrim($fields_string, '&');
 }
 if($type=="GET" && count($params)!=0){
  $url.="?".$fields_string;
 }
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 if($type=="POST" && count($params)!=0){
  curl_setopt($ch, CURLOPT_POST, count($params));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
 }
 $output = curl_exec($ch);
 return $output;
}
function filt($string){
 return htmlspecialchars(urldecode($string));
}
function getData($id, $key=""){
 if(!$GLOBALS['db']->db){
  return false;
 }else{
  return $GLOBALS['db']->getData($id, $key);
 }
}
function saveData($id, $key="", $value=""){
 if(!$GLOBALS['db']->db){
  return false;
 }else{
  return $GLOBALS['db']->saveData($id, $key, $value);
 }
}
function removeData($id, $key=""){
 if(!$GLOBALS['db']->db){
  return false;
 }else{
  return $GLOBALS['db']->removeData($id, $key);
 }
}
?>
