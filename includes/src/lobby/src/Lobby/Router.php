<?php
/**
 * Lobby\Router
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/Router.php
 */

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
  
  /**
   * Set up
   */
  public static function __constructStatic(){
    self::$router = new Klein();
  }
  
  /**
   * Set a route
   * @param string $route The route path
   * @param string $callback Function to handle the route
   */
  public static function route($route, $callback) {
    self::$router->respond($route, $callback);
  }
  
  /**
   * Dispatch all routes and send response.
   * 
   * All routes are ran and if there is content, a response is sent.
   * 
   * If it's a request to a native file, FALSE is returned.
   * 
   * @return bool Whether a route is set to handle this request
   */
  public static function dispatch(){
    self::defaults();
    \Hooks::doAction("router.finish");
    self::$router->dispatch(null, null, false);
    
    if(Response::hasContent()){
      Response::send();
      return true;
    }else if(self::pathExists()){
      return false;
    }else{
      Response::showError();
      return true;
    }
  }
  
  /**
   * Default routes and settings
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
  
  /**
   * This is useful when Lobby is run using PHP Built In Server
   * When no routes are matched, by default a 404 is inited,
   * even when the file exists in Lobby as .php file. To prevent
   * this, we check if the file exist and return false to the PHP
   * Built in Server to make it serve the file normally
   * http://php.net/manual/en/features.commandline.webserver.php#example-430
   * 
   * @return bool Whether the request points to a file
   */
  public static function pathExists(){
    if(\Lobby\FS::rel($_SERVER['PHP_SELF']) !== "index.php"){
      /**
       * The path should point to a file and not directory index
       */
      return file_exists(L_DIR . $_SERVER['PHP_SELF']) && !is_dir(L_DIR . $_SERVER['PHP_SELF']);
    }
    return false;
  }
  
}
