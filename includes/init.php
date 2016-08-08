<?php
/**
 * Add the <head> files if it's not the install page
 */
if(!\Lobby::status("lobby.install")){
  /**
   * Left Menu
   */
  \Lobby\UI\Panel::addTopItem("lobbyHome", array(
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
    "app_manager" => array(
      "text" => "Apps",
      "href" => "/admin/apps.php"
    ),
    "lobby_store" => array(
      "text" => "Lobby Store",
      "href" => "/admin/lobby-store.php",
    ),
    "about" => array(
      "text" => "Settings",
      "href" => "/admin/settings.php"
    )
  );
  \Lobby\UI\Panel::addTopItem("lobbyAdmin", $adminArray);
  
  if(\Lobby\FS::exists("/upgrade.lobby")){
    require_once L_DIR . "/includes/src/Update.php";
    $l_info = json_decode(\Lobby\FS::get("/lobby.json"));
    
    if($lobby_version != $l_info->version){
      Lobby\DB::saveOption("lobby_latest_version", $l_info->version);
      Lobby\DB::saveOption("lobby_latest_version_release", $l_info->released);
    }
    \Lobby\Update::finish_software_update();
  }
}

if(\Lobby::status("lobby.admin")){
  /**
   * Add Admin Pages' stylesheet, script
   */
  \Assets::js("admin", "/admin/js/admin.js");
  
  /**
   * Add sidebar
   */
  Hooks::addAction("admin.body.begin", function(){
    require L_DIR . "/admin/inc/sidebar.php";
  });
  
  /**
   * Add sidebar handler in panel
   */
  \Hooks::addAction("panel.end", function(){
    echo '<a href="#" data-activates="slide-out" class="button-collapse"><i class="mdi-navigation-menu"></i></a>';
  });
  
  /**
   * Check For New Versions (Apps & Core)
   */
  if(\Lobby::getConfig('server_check') === true && !isset($_SESSION['checkedForLatestVersion'])){
    \Lobby\Server::check();
    $_SESSION['checkedForLatestVersion'] = 1;
  }
}

/**
 * Insert Lobby Info to JS Files
 */
\Hooks::addAction("head.begin,admin.head.begin", function(){
?>
  <script>
    window.tmp = {};
    window.lobbyExtra = {
      url: "<?php echo L_URL;?>",
      csrfToken: "<?php echo CSRF::get();?>",
      sysInfo: {
        os: "<?php echo \Lobby::getSysInfo("os");?>"
      }
    };
    <?php
    if(\Lobby\Apps::isAppRunning()){
      echo 'window.lobbyExtra["app"] = {
        id: "'. \Lobby\Apps::getInfo("id") .'",
        url: "'. \Lobby\Apps::getInfo("url") .'",
        src: "'. \Lobby\Apps::getInfo("srcURL") .'"
      };';
    }
  ?></script>
<?php
});
