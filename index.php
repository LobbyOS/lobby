<?php
require_once "load.php";
require_once L_ROOT . "/includes/vendor/autoload.php";

/* Make the request URL relative to the base URL of Lobby installation. http://localhost/lobby will be changed to "/" and http://lobby.local to "/" */
$lobbyBase = str_replace($_SERVER['DOCUMENT_ROOT'], "", $docRoot);
$_SERVER['REQUEST_URI'] = str_replace($lobbyBase, "", $_SERVER['REQUEST_URI']);

$router = new \Klein\Klein();
$GLOBALS['workspaceHTML'] = "";

/* Route App Pages (/app/{appname}/{page}) to according apps */
$router->with('/app', function () use ($router, $LC, $LD) {
	$router->respond("/[a:appID]/?[:page]?", function($request) use ($LC, $LD){
		$AppID 			  = $request->appID;
		$GLOBALS['AppID'] = $AppID;
		$page  			  = $request->page != "" ? $request->page : "/";
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
  				"text"	  => $AppInfo['name'],
  				"href"	  => APP_URL,
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
	$LC->addStyle("dashboard", L_HOST."/includes/source/css/dashboard.css");
	$LC->setTitle("Dashboard");
	$GLOBALS['workspaceHTML'] = array(L_ROOT . "/includes/source/main.php");
});

/* The default 404 pages */
$router->respond('404', function () {
    ser();
});
/* Finish the Routing */
$router->dispatch();

if($GLOBALS['workspaceHTML'] != "" || is_array($GLOBALS['workspaceHTML'])){
	include L_ROOT . "/includes/source/page.php";
}else{
	ser();
}
?>