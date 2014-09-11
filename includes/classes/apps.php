<?php
class App extends L{
 
 private $app;
 public $appDir;
 public $exists = false;
 public $appInfo;
 
 public function __construct($name=""){
  	if($name != ""){
		$appDir = APPS_DIR . "/$name";
		$this->app 	  = $name;
		$this->appDir = $appDir;
   	
		if( $this->checkAppExistCriteria() ){
    		/* Insert the App Manifest Info into the class object */
    		$this->getInfo();
    		$this->exists = true;
    		return true;
		}else{
    		$this->exists = false;
    		if( $this->disableApp() ){
    			$this->log("App $name was disabled because it was not a valid App.");
    		}
    		
    		return false;
		}
  	}
 }
 
 /* Returns Enabled Apps as an array */
 public function getEnabledApps(){
  	$apps	= $GLOBALS['db']->getOption("active_apps");
  	$apps	= json_decode($apps, true);
  	if(count($apps) == 0){
		return array();
  	}else{
		return $apps;
  	}
 }
 
 /* Returns Disiabled Apps as an array */
 public function getDisabledApps(){
  	$disApps		 = array();
  	$enabledApps = $this->getEnabledApps();

  	foreach($this->getApps() as $app){
		if(array_search($app, $enabledApps) === false){
    		$disApps[] = $app;
		}
  	}
  	return $disApps;
 }
 
 /* Returns boolean of installation status */
 public function isEnabled(){
  	$enabledApps = $this->getEnabledApps();
  	return array_search($this->app, $enabledApps) === false ? false:true;
 }
 
 /* Get the manifest info of app as array */
 public function getInfo(){
  	if(!is_array($this->appInfo)){
  		$manifest = file_exists($this->appDir . "/manifest.json") ? file_get_contents($this->appDir . "/manifest.json"):false;
  		if($manifest){
			$details = json_decode($manifest, true);
   		
			/* Add extra info with the manifest info */
			$details['id']		 = $this->app;
			$details['location'] = $this->appDir;
			$details['URL'] 	 = $this->getURL(true);
   		
			/* Insert the info as a property */
			$this->appInfo = $details;
   		
			return $details;
  		}else{
			return false;
  		}
  	}else{
  		return $this->appInfo;
  	}
 }
 
 /* Get the apps that are in the directory as array */
 public function getApps(){
  	$appFolders = array_diff(scandir(APPS_DIR), array('..', '.'));
  	$apps 		= array();
  	
  	foreach($appFolders as $appFolder){
		if( $this->checkAppExistCriteria($appFolder) ){
    		$apps[] = $appFolder;
		}
  	}
  	return $apps;
 }
 
 /* Enable the app */
 public function enableApp(){
  	if( $this->app ){
		$apps = $GLOBALS['db']->getOption("active_apps");
		$apps = json_decode($apps, true);
		if($apps == null){
			$apps = array();
		}
		if(array_search($this->app, $apps) === false){
    		array_push($apps, $this->app);
    		$GLOBALS['db']->saveOption("active_apps", json_encode($apps));
    		return true;
		}else{
    		return true; // App Is Already Enabled. So we don't need give out the boolean false.
		}
  	}else{
		return false;
  	}
 }
 
 /* Disable the app */
 public function disableApp(){
  	if($this->app && $this->isEnabled()){
		$apps = $GLOBALS['db']->getOption("active_apps");
		$apps = json_decode($apps, true);
		$key  = array_search($this->app, $apps);
		if($key !== false){
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
 
 /* Get the Web URL of App */
 public function getURL($pluginDir = false){
  	return $pluginDir === true ? L_HOST . "/contents/apps/{$this->app}" : L_HOST . "/app/{$this->app}";
 }
 
 /* Remove the app files recursilvely from the directory and disable the app */
 public function removeApp(){
  	if($this->app){
		$apps = $this->getApps();
		$key  = array_search($this->app, $apps);
		if($key !== false){
    		unset($apps[$key]);
    		$this->disableApp();
    		$dir = $this->appDir;
    		$files = array_diff(scandir($dir), array('.', '..'));
    		foreach ($files as $file) { 
				if( is_dir("$dir/$file") ){
					delTree("$dir/$file");
				}else{
					unlink("$dir/$file");
				}
    		} 
    		return rmdir($dir);
		}else{
    		return false;
		}
  	}else{
		return false;
  	}
 }
 
 /* Return the app class object */
 public function run($LC){
 	if($this->app){
 		require_once L_ROOT . "/includes/classes/app.php";
 		require_once $this->appDir . "/program.php";
 		
 		$appInfo   = $this->getInfo();
 		$className = str_replace("-", "DASH", $this->app);
 		
 		/* Create the App Program Object */
 		$program = new ReflectionClass( $className );
 		
 		/* Make the class object */
 		$class = $program->newInstanceArgs();
 		/* Send app details and LC object to the AppProgram */
 		$class->setTheVars($LC, $appInfo);
 		
 		/* Return the App Object */
		return $class;
 	}
 }
 
 /* Since we check if App is valid in multiple places, we make it into a function */
 public function checkAppExistCriteria($name = ""){
 	$appDir = $name == "" ? $this->appDir : APPS_DIR . "/$name";
 	
 	return is_dir($appDir) && file_exists("$appDir/manifest.json") && file_exists("$appDir/program.php") ? true : false;
 }
}
?>