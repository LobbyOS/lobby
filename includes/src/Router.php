<?php
namespace Lobby;
require_once L_DIR . "/includes/src/composer/vendor/autoload.php";

/**
 * The Router class for Routing paths coming to Lobby accordingly
 * Klein is used as the Routing Library, so Route names and others
 * will be as Klein's : https://github.com/chriso/klein.php
 */
class Router {
  
  public static $router;
  
  public static function init(){
    self::$router = new \Klein\Klein();
  }
  
  public static function route($route, $callback) {
    self::$router->respond($route, function($request, $response) use($callback) {
      $return = $callback($request, $response);
      if($return !== false && $GLOBALS['workspaceHTML'] == ""){
        $GLOBALS['route_active'] = 1;
      }
    });
  }
  
  public static function dispatch(){
    self::defaults();
    \Lobby::doHook("router.finish");
    self::statusRoutes();
    self::$router->dispatch();
  }
  
  /**
   * Define some pages by default
   */
  public static function defaults(){
    /**
     * Route App Pages (/app/{appname}/{page}) to according apps
     */
    self::route("/app/[:appID]?/[**:page]?", function($request){
      $AppID = $request->appID;
      $GLOBALS['AppID'] = $AppID;
      $page = $request->page != "" ? "/{$request->page}" : "/";
      
      /**
       * Check if App exists
       */
      $App = new \Lobby\Apps($AppID);
      if($App->exists && $App->isEnabled() && substr($page, 0, 7) != "/Admin/"){
        $class = $App->run();
        $AppInfo = $App->info;
      
        /**
         * Set the title
         */
        \Lobby::setTitle($AppInfo['name']);
          
        /**
         * Add the App item to the navbar
         */
        \Lobby\Panel::addTopItem("lobbyApp{$AppID}", array(
          "text" => $AppInfo['name'],
          "href" => APP_URL,
          "position" => "left"
        ));
        $page_response = $class->page($page);
        
        if($page_response == "auto"){
          if($page == "/"){
            $page = "/index";
          }
          $GLOBALS['workspaceHTML'] = $class->inc("/src/Page{$page}.php");
        }else{
          $GLOBALS['workspaceHTML'] = $page_response;
        }
        if($GLOBALS['workspaceHTML'] == null){
          ser();
        }
      }else{
        ser();
      }
    });
    
    /**
     * Dashboard Page
     * The main Page. Add CSS & JS accordingly
     */
    self::route("/", function() {
      \Lobby::addScript("metrojs", "/includes/lib/metrojs/metrojs.js");
      \Lobby::addStyle("metrojs", "/includes/lib/metrojs/metrojs.css");
      \Lobby::addScript("dynscroll", "/includes/lib/scrollbar/scrollbar.js");
      \Lobby::addStyle("dynscroll", "/includes/lib/scrollbar/scrollbar.css");
      \Lobby::addScript("dashboard", "/includes/lib/core/JS/dashboard.js");
      \Lobby::addStyle("dashboard", "/includes/lib/core/CSS/dashboard.css");
      \Lobby::setTitle("Dashboard");
      $GLOBALS['workspaceHTML'] = array("/includes/lib/core/Inc/main.php");
    });
    
    /**
     * Administration
     */
    self::route("/admin/app/[:appID]?/[**:page]?", function($request){
      $AppID = $request->appID;
      $GLOBALS['AppID'] = $AppID;
      $page = $request->page != "" ? "/Admin/{$request->page}" : "/Admin/index";

      /**
       * Check if App exists
       */
      $App = new \Lobby\Apps($AppID);
      if($App->exists && $App->isEnabled()){
        /**
         * Redirect /src/ files to App's Source in /contents folder
         */
        $class = $App->run();
        $AppInfo = $App->info;
      
        /**
         * Set the title
         */
        \Lobby::setTitle($AppInfo['name']);
        
        /**
         * Add the App item to the navbar
         */
        \Lobby\Panel::addTopItem("lobbyApp{$AppID}", array(
          "text" => "Admin > " . $AppInfo['name'],
          "href" => "/admin/app/$AppID",
          "position" => "left"
        ));
        
        $page_response = $class->page($page);
        if($page_response == "auto"){
          if($page == "/"){
            $page = "/index";
          }
          $GLOBALS['workspaceHTML'] = $class->inc("/src/Page{$page}.php");
        }else{
          $GLOBALS['workspaceHTML'] = $page_response;
        }
        
        if($GLOBALS['workspaceHTML'] === false || $GLOBALS['workspaceHTML'] == null){
          ob_start();
            ser("Error", "The app '<strong>{$AppID}</strong>' does not have an Admin Page");
          $error = ob_get_contents();
          ob_end_clean();
          
          $GLOBALS['workspaceHTML'] = "<div class='contents'>". $error ."</div>";
        }
      }
    });
  }
  
  public static function statusRoutes(){
    /**
     * The default 404 page
     */
    self::$router->onHttpError(function ($code, $router) {
      if($code == 404){
        ser();
      }
    });
  }
}
\Lobby\Router::init();
