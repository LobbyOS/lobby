<?php
namespace Lobby\Module;

class admin extends \Lobby\Module {
  
  public function init(){
    if(\Lobby::status("lobby.assets-serve") === false){
      $this->install();
      
      require_once __DIR__ . "/inc/config.php";
      $this->routes();
      
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
          \Lobby\UI\Panel::addTopItem('adminModule', array(
            "text" => "<img src='". $this->url ."/image/admin.svg' style='width: 40px;height: 40px;' />",
            "href" => "/",
            "position" => "left",
            "subItems" => array(
              "changePassword" => array(
                "text" => "Change Password",
                "href" => "/admin/ChangePassword",
              ),
              'LogOut' => array(
                "text" => "Log Out",
                "href" => "/admin/login?logout"
              )
            )
          ));
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
          unset(\Lobby\UI\Panel::$top_items['left']['lobbyAdmin']);
        });
      }
    
    }
  }
  /**
   * Install module
   * --------------
   * Create the `users` table
   */
  public function install(){
    if(getOption("admin_installed") == null && \Lobby::$installed){
      /**
       * Install Module
       */
      $salt = \H::randStr(15);
      $cookie = \H::randStr(15);
      saveOption("admin_secure_salt", $salt);
      saveOption("admin_secure_cookie", $cookie);
      
      $prefix = \Lobby\DB::$prefix;
      /**
       * Create `users` TABLE
       */
      $sql = \Lobby\DB::$dbh->prepare("CREATE TABLE IF NOT EXISTS `{$prefix}users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(10) NOT NULL,
        `email` tinytext NOT NULL,
        `password` varchar(64) NOT NULL,
        `password_salt` varchar(20) NOT NULL,
        `name` varchar(30) NOT NULL,
        `created` datetime NOT NULL,
        `attempt` varchar(15) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
      if($sql->execute() != 0){
        saveOption("admin_installed", "true");
      }
    }
  }
  
  /**
   * Add routes
   */
  public function routes(){
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
  }
}
