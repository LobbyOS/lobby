<?php
/* Check For New Versions (Apps & Lobby) */
if(!isset($_SESSION['checkedForLatestVersion'])){
 	$App	  = new App();
 	$response = $LC->loadURL(L_SERVER . "/latestVersion", array(
  		"apps" => implode(",", $App->getApps())
 	), "POST");
 	
 	if($response){
  		$response = json_decode($response, true);
  		saveOption("lobby_latest_version", $response['version']);
  		saveOption("lobby_latest_version_release", $response['released']);
  		
  		if(isset($response['apps']) && count($response['apps']) != 0){
   		$AppUpdates = array();
   		foreach($response['apps'] as $appID => $latestVersion){
    			$App = new App($appID);
    			$AppInfo = $App->getInfo();
    			if($AppInfo['version'] != $latestVersion){
     				$AppUpdates[$appID] = $latestVersion;
    			}
   		}
   		saveOption("app_updates", json_encode($AppUpdates));
  		}
 	}
 	$_SESSION['checkedForLatestVersion'] = 1;
}

/* Default Styles */
$LC->addStyle( "main", Helpers::URL("/includes/source/css/main.css") );

if(Helpers::curPage() != "admin/install.php"){
 	/* Styles */
 	$LC->addStyle( "jqueryui", Helpers::URL("/includes/source/css/jquery-ui.css") ); // jQuery UI
 	$LC->addStyle( "home", Helpers::URL("/includes/source/css/home.css") );
 	$LC->addStyle( "panels", Helpers::URL("/includes/source/css/panels.css") );
 
 	/* Scripts */
 	$LC->addScript( "jquery", Helpers::URL("/includes/source/js/jquery.js") );
 	$LC->addScript( "jqueryui", Helpers::URL("/includes/source/js/jquery-ui.js") ); // jQuery UI
 	$LC->addScript( "main", Helpers::URL("/includes/source/js/main.js") );
 	$LC->addScript( "superfish", Helpers::URL("/includes/source/js/superfish.js") );
 	$LC->addScript( "home", Helpers::URL("/includes/source/js/home.js") );
 
 	/* Design */
 		/*Left Menu*/
  		$LD->addTopItem("lobbyHome", array(
  			"text"	  => "Home",
  			"href"	  => L_HOST,
  			"position" => "left"
  		));
  		$LD->addTopItem("lobbyAdmin", array(
   		"text"	  		 => "Admin",
   		"href"           => Helpers::URL("admin"),
   		"subItems" 		 => array(
   			"AppManager" => array(
   				"text"	 => "App Manager",
   				"href"	 => Helpers::URL("admin/apps.php")
   			),
   			"AppCenter"  => array(
   				"text"	 => "App Center",
   				"href"	 => Helpers::URL("/admin/appCenter.php"),
   			),
   			"About" 	 => array(
   				"text"	 => "About",
   				"href"	 => Helpers::URL("/admin/about.php")
   			)
   		),
   		"position" 		 => "left"
  		));
  
 		/*Right Menu*/
  			$LD->addTopItem("netStatus", array(
  				"html"	   => "<span id='net' title='Online'></span>",
  				"position" => "right"
  			));
  			
  	$AppUpdates		= json_decode(getOption("app_updates"), true);
  	$latestVersion 	= getOption("lobby_latest_version");
  	if((isset($AppUpdates) && count($AppUpdates) != 0) || ($latestVersion && getOption("lobby_version") != $latestVersion)){
		$LD->addTopItem("upgradeNotify", array(
			"html" 	   => Helpers::link("/admin/upgrade.php", "<span id='upgrade' title='An Update Is Available'></span>"),
			"position" => "right"
		));
  	}
}

if( Helpers::curPage() == "admin/" ){
	
}
?>