<?php
namespace Lobby;

use Klein\Klein;
use Response;

/**
 * The Router class for Routing paths coming to Lobby accordingly
 * Klein is used as the Routing Library, so Route names and others
 * will be as Klein's : https://github.com/chriso/klein.php
 */
class Router {
  
  public static $router;
  
  public static function __constructStatic(){
    self::$router = new Klein();
  }
  
  public static function route($route, $callback) {
    self::$router->respond($route, $callback);
  }
  
  public static function dispatch(){
    self::defaults();
    \Hooks::doAction("router.finish");
    self::statusRoutes();
    self::$router->dispatch(null, null, false);
    
    if(Response::hasContent()){
      Response::send();
    }else if(self::pathExists()){
      return false;
    }else{
      Response::showError();
    }
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
      $page = $request->page != "" ? "/{$request->page}" : "/";
      
      /**
       * Check if App exists
       */
      $App = new \Lobby\Apps($AppID);
      if($App->exists && $App->enabled){
        $class = $App->run();
        $AppInfo = $App->info;
      
        /**
         * Set the title
         */
        Response::setTitle($AppInfo['name']);
          
        /**
         * Add the App item to the navbar
         */
        \Lobby\UI\Panel::addTopItem("lobbyApp{$AppID}", array(
          "text" => $AppInfo['name'],
          "href" => $AppInfo['url'],
          "subItems" => array(
            "app_admin" => array(
              "text" => "Admin",
              "href" => "/admin/apps.php?app=$AppID"
            ),
            "app_disable" => array(
              "text" => "Disable",
              "href" => "/admin/apps.php?action=disable&app=$AppID" . \CSRF::getParam()
            ),
            "app_remove" => array(
              "text" => "Remove",
              "href" => "/admin/apps.php?action=remove&app=$AppID" . \CSRF::getParam()
            )
          ),
          "position" => "left"
        ));
        $pageContent = $class->getPageContent($page);
        if($pageContent !== null)
          Response::setPage($pageContent);
      }else{
        echo ser();
      }
    });
    
    /**
     * Dashboard Page
     * The main Page. Add CSS & JS accordingly
     */
    self::route("/", function() {
      Response::setTitle("Dashboard");
      \Lobby\UI\Themes::loadDashboard("head");
      Response::loadPage("/includes/lib/lobby/inc/dashboard.php");
    });
  }
  
  public static function statusRoutes(){
    /**
     * The default 404 page
     */
    self::$router->onHttpError(function ($code, $router) {
      if($code === 404){
        if(self::pathExists()){
          $router->response()->code(200);
        }else{
          echo ser();
        }
      }
    });
  }
  
  /**
   * This is useful when Lobby is run using PHP Built In Server
   * When no routes are matched, by default a 404 is inited,
   * even when the file exists in Lobby as .php file. To prevent
   * this, we check if the file exist and return false to the PHP
   * Built in Server to make it serve the file normally
   * http://php.net/manual/en/features.commandline.webserver.php#example-430
   */
  public static function pathExists(){
    if(\Lobby\FS::rel($_SERVER['PHP_SELF']) !== "index.php"){
      return file_exists(L_DIR . $_SERVER['PHP_SELF']);
    }
    return false;
  }
  
}
