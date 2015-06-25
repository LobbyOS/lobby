<?php
require_once __DIR__ . "/config.php";

/**
 * Add the Login Page in /admin/login route
 */
\Lobby\Router::route("/admin/login", function(){
  if(\Fr\LS::userExists("admin") === false){
    \Fr\LS::register("admin", "admin", array(
      "name" => "Admin",
      "created" => date("Y-m-d H:i:s")
    ));
  }
  include __DIR__ . "/page/login.php";
});

/**
 * Add the Change Password Page in /admin/ChangePassword route
 */
\Lobby\Router::route("/admin/ChangePassword", function(){
  include __DIR__ . "/page/change_password.php";
});

if(\Fr\LS::$loggedIn){
  /**
   * Logged In
   */
  \Lobby::hook("init", function(){
    /**
     * Add Change Password Item in Top Panel -> Admin before Log Out item
     * This is done by first removing the Log Out item, adding the Change
     * Password item and then adding back the Log Out item
     */
    \Lobby\Panel::$top_items['left']['lobbyAdmin']['subItems']['ChangePassword'] = array(
      "text" => "Change Password",
      "href" => "/admin/ChangePassword"
    );
    \Lobby\Panel::$top_items['left']['lobbyAdmin']['subItems']['LogOut'] = array(
      "text" => "Log Out",
      "href" => "/admin/login?logout"
    );
  });
}else{
  /**
   * Not logged in
   */
  if(\Lobby\Modules::exists("indi") === false){
    if(\Lobby::curPage() != "/admin/login" && !\Lobby::status("lobby.install")){
      \Lobby::redirect("/admin/login");
    }
  }else{
    if(\Lobby::curPage() != "/admin/login" && \Lobby::curPage() != "/admin/install.php" && substr(\Lobby::curPage(), 0, 6) == "/admin"){
      \Lobby::redirect("/admin/login");
    }
  }
  \Lobby::hook("init", function(){
    unset(\Lobby\Panel::$top_items['left']['lobbyAdmin']);
  });
}
