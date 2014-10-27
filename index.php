<?php
require_once "load.php";
require_once L_ROOT . "/includes/vendor/autoload.php";

$router = new \Klein\Klein();
$GLOBALS['workspaceHTML'] = "";

/* Route App Pages (/app/{appname}/{page}) to according apps */
$router->with('/app', function () use ($router, $LC, $LD) {
	$router->respond("/[:appID]?/[**:page]?", function($request) use ($LC, $LD){
		$AppID 			  = $request->appID;
		$GLOBALS['AppID'] = $AppID;
		$page  			  = $request->page != "" ? "/{$request->page}" : "/";
		/* Check if App exists */
		$App = new App($AppID);
		
		if($App->exists && $App->isEnabled()){
			$class   = $App->run($LC);
			$AppInfo = $App->getInfo();
			
			/* Define the App Constants */
			define("APP_DIR", $App->appDir);
  			define("APP_URL", L_HOST . "/app/$AppID");
  			define("APP_SOURCE", APPS_URL . "/$AppID");
  			
  			/* Set the title */
  			$LC->setTitle($AppInfo['name']);
  			/* Add the App item to the navbar */
  			$LD->addTopItem("lobbyApp{$AppID}", array(
  				"text"	   => $AppInfo['name'],
  				"href"	   => APP_URL,
  				"position" => "left"
  			));
  			
			$GLOBALS['workspaceHTML'] = $class->page($page);
		}else{
			ser();
		}
	});
});

/* Route / to Dashboard Page */
$router->respond("/", function() use ($LC) {
	$LC->addScript("metrojs", "/includes/lib/metrojs/metrojs.js");
	$LC->addStyle("metrojs", "/includes/lib/metrojs/metrojs.css");
	$LC->addScript("dynscroll", "/includes/lib/scrollbar/scrollbar.js");
	$LC->addStyle("dynscroll", "/includes/lib/scrollbar/scrollbar.css");
	$LC->addScript("dashboard", "/includes/lib/core/dashboard.js");
	$LC->addStyle("dashboard", "/includes/lib/core/dashboard.css");
	$LC->setTitle("Dashboard");
	$GLOBALS['workspaceHTML'] = array(L_ROOT . "/includes/lib/core/php/main.php");
});

/* The default 404 pages */
$router->respond('404', function () {
    ser();
});
/* Finish the Routing */
$router->dispatch();

if($GLOBALS['workspaceHTML'] != "" || is_array($GLOBALS['workspaceHTML'])){
	include L_ROOT . "/includes/lib/core/php/page.php";
}else{
	ser();
}
?>