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
   * Add Left Panel items
   */
  \Lobby\UI\Panel::addLeftItem("lobby-link", array(
    "html" => "<a target='_blank' href='http://lobby.subinsb.com'>Lobby ". \Lobby::getVersion(true) ."</a>"
  ));

  $links = array(
    "/admin/index.php" => "Dashboard",
    "/admin/apps.php" => "Apps",
    "/admin/lobby-store.php" => "Lobby Store",
    "/admin/settings.php" => "Settings",
    "/admin/modules.php" => "Modules",
    "/admin/update.php" => "Updates"
  );
  $links = Hooks::applyFilters("admin.view.sidebar", $links);

  $curPage = \Lobby::curPage();
  foreach($links as $link => $text){
    \Lobby\UI\Panel::addLeftItem("admin-nav-" . strtolower($text), array(
      "text" => $text,
      "href" => $link,
      "class" => (substr($curPage, 0, strlen($link)) === $link || ($curPage == "/admin/install-app.php" && $text == "Apps")) ? "active" : null
    ));
  }

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
