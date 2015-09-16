<?php
/**
 * Default Styles
 */
\Lobby::addStyle("home", "/includes/lib/core/CSS/font.css");
\Lobby::addStyle("main", "/includes/lib/core/CSS/main.css");

/**
 * Some checking to make sure Lobby works fine
 */
if(!is_writable(L_DIR) || !is_writable(APPS_DIR)){
  $GLOBALS['initError'] = array("Wrong Permissions", "The permission of Lobby is not correct. All you have to do is change the permission of <blockquote>". L_DIR ."</blockquote>to read and write (0775).");
  
  if(\Lobby::$sysinfo['os'] == "linux"){
    $GLOBALS['initError'][1] = $GLOBALS['initError'][1] . "<p clear>On Linux systems, do this in terminal : <blockquote>sudo chown \${USER}:www-data ". L_DIR ." -R && sudo chmod 0775 ". L_DIR ." -R</blockquote></p>";
  }
}
if(isset($GLOBALS['initError'])){
  echo "<html><head>";
    \Lobby::$js = array();
    \Lobby::head();
  echo "</head><body><div class='workspace'><div class='contents'>";
    ser($GLOBALS['initError'][0], $GLOBALS['initError'][1]);
  echo "</div></div></body></html>";
  exit;
}

/* Add the <head> files if it's not the install page */
if(\Lobby::curPage() != "/admin/install.php"){
  /* Styles */
  \Lobby::addStyle( "jqueryui", "/includes/lib/jquery/jquery-ui.css"); // jQuery UI
 
  /* Scripts */
  \Lobby::addScript("jquery", "/includes/lib/jquery/jquery.js");
  \Lobby::addScript("jqueryui", "/includes/lib/jquery/jquery-ui.js"); // jQuery UI
  \Lobby::addScript("main", "/includes/lib/core/JS/main.js");

  /*Left Menu*/
  \Lobby\Panel::addTopItem("lobbyHome", array(
    "text" => "Home",
    "href" => L_URL,
    "position" => "left"
  ));
  $adminArray = array(
    "text" => "Admin",
    "href" => "/admin",
    "position" => "left"
  );
  $adminArray["subItems"] = array(
    "AppManager" => array(
      "text" => "Apps",
      "href" => "/admin/apps.php"
    ),
    "LobbyStore" => array(
      "text" => "Lobby Store",
      "href" => "/admin/lobby-store.php",
    ),
    "About" => array(
      "text" => "About",
      "href" => "/admin/about.php"
    )
  );
  \Lobby\Panel::addTopItem("lobbyAdmin", $adminArray);
  
  /**
   * If there is a update available either app or core, add an 
   * "Update Available" icon on the right side of panel
   */
  $AppUpdates = json_decode(getOption("app_updates"), true);
  $lobby_version = getOption("lobby_version");
  $latestVersion = getOption("lobby_latest_version");
  
  if(\Lobby\FS::exists("/upgrade.lobby")){
    require_once L_DIR . "/includes/src/Update.php";
    $l_info = json_decode(\Lobby\FS::get("/lobby.json"));
    
    if($lobby_version != $l_info->version){
      saveOption("lobby_latest_version", $l_info->version);
      saveOption("lobby_latest_version_release", $l_info->released);
    }
    \Lobby\Update::finish_software_update();
  }
  
  if((count($AppUpdates) != 0) || ($latestVersion && $lobby_version != $latestVersion)){
    \Lobby\Panel::addTopItem("updateNotify", array(
      "html" => \Lobby::l("/admin/update.php", "<span id='update' title='An Update Is Available'></span>"),
      "position" => "right"
    ));
  }
}
if(\Lobby::status("lobby.install")){
  \Lobby::addStyle("admin", "/includes/lib/core/CSS/admin.css");
}
if(\Lobby::status("lobby.admin")){
  /**
   * Add Admin Pages' stylesheet
   */
  \Lobby::addStyle("admin", "/includes/lib/core/CSS/admin.css");
  
  /**
   * Check For New Versions (Apps & Core)
   */
  if(\Lobby::$config['server_check'] === true && !isset($_SESSION['checkedForLatestVersion'])){
    \Lobby\Server::check();
    $_SESSION['checkedForLatestVersion'] = 1;
  }
}
?>
