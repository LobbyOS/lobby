<?
class App extends L{
 private $app;
 public $exists=false;
 function __construct($name=""){
  if($name!=""){
   if(!file_exists(APP_DIR."$name/manifest.json")){
    $this->exists=false;
    return false;
   }else{
    $this->app=$name;
    $this->exists=true;
   }
  }
 }
 function getInfo(){
  $name=$this->app;
  $manifest=file_get_contents(APP_DIR."$name/manifest.json");
  if($manifest){
   $details=json_decode($manifest, true);
   $details['location']=APP_DIR."$name/";
   return $details;
  }else{
   return false;
  }
 }
 function getApps(){
  $appFolders = array_diff(scandir(APP_DIR), array('..', '.'));
  $apps=array();
  foreach($appFolders as $v){
   if(is_dir(APP_DIR.$v) && file_exists(APP_DIR."$v/manifest.json")){
    $apps[]=$v;
   }
  }
  return $apps;
 }
 function enableApp(){
  if($this->app){
   $apps=$GLOBALS['db']->getOption("active_apps");
   $apps=json_decode($apps, true);
   if($apps==null){$apps=array();}
   if(array_search($this->app, $apps)===false){
    array_push($apps, $this->app);
    $GLOBALS['db']->saveOption("active_apps", json_encode($apps));
    return true;
   }else{
    return true;// App Is Already Enabled. So we don't need give out the boolean false.
   }
  }else{
   return false;
  }
 }
 function disableApp(){
  if($this->app){
   $apps=$GLOBALS['db']->getOption("active_apps");
   $apps=json_decode($apps, true);
   $key=array_search($this->app, $apps);
   if($key!==false){
    unset($apps[$key]);
    $GLOBALS['db']->saveOption("active_apps", json_encode($apps));
    return true;
   }else{
    return false;
   }
  }else{
   return false;
  }
 }
 public function getURL(){
  return L_HOST."/app/".$this->app;
 }
}
?>
