<?php
/**
 * Some checking to make sure Lobby works fine
 */
if(!is_writable(L_DIR) || !is_writable(APPS_DIR)){
  $error = array("Wrong Permissions", "The permission of Lobby is not correct. All you have to do is change the permission of <blockquote>". L_DIR ."</blockquote>to read and write (0755).");
  
  if(\Lobby::getSysInfo("os") === "linux"){
    $error[1] .= "<p clear>On Linux systems, do this in terminal : <blockquote>sudo chown \${USER}:www-data ". L_DIR ." -R && sudo chmod u+rwx,g+rw,o+r ". L_DIR ." -R</blockquote></p>";
  }
  Response::showError($error[0], $error[1]);
}

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
      saveOption("lobby_latest_version", $l_info->version);
      saveOption("lobby_latest_version_release", $l_info->released);
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
   * Add sidebar handler in panel
   */
  \Lobby::hook("panel.end", function(){
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
\Lobby::hook("head.begin,admin.head.begin", function(){
?>
  <script>
    window.tmp = {};
    window.lobbyExtra = {
      url: "<?php echo L_URL;?>",
      csrf_token: "<?php echo csrf("s");?>",
      sysInfo: {
        os: "<?php echo \Lobby::getSysInfo("os");?>"
      }
    };
    <?php
    if(\Lobby\Apps::$appID){
      echo 'window.lobbyExtra["app"] = {
        id: "'. \Lobby\Apps::$appID .'",
        url: "'. APP_URL .'",
        src: "'. \Lobby::u("/contents/apps/" . \Lobby\Apps::$appID) .'"
      };';
    }
  ?></script>
<?php
});
