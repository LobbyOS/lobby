<?php
namespace Lobby;

use Response;

/**
 * The Router class for Routing paths coming to Lobby accordingly
 * Klein is used as the Routing Library, so Route names and others
 * will be as Klein's : https://github.com/chriso/klein.php
 */
class Router {
  
  public static $router;
  public static $routeActive = false;
  
  public static function __constructStatic(){
    self::$router = new \Klein\Klein();
  }
  
  public static function route($route, $callback) {
    self::$router->respond($route, function($request, $response) use($callback) {
      $return = $callback($request, $response);
      if($return !== false && !Response::hasContent()){
        self::$routeActive = true;
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
      $page = $request->page != "" ? "/{$request->page}" : "/";
      
      /**
       * Check if App exists
       */
      $App = new \Lobby\Apps($AppID);
      if($App->exists && $App->enabled && substr($page, 0, 7) != "/Admin/"){
        $class = $App->run();
        $AppInfo = $App->info;
      
        /**
         * Set the title
         */
        \Lobby::setTitle($AppInfo['name']);
          
        /**
         * Add the App item to the navbar
         */
        \Lobby\UI\Panel::addTopItem("lobbyApp{$AppID}", array(
          "text" => $AppInfo['name'],
          "href" => APP_URL,
          "subItems" => array(
            "app_admin" => array(
              "text" => "Admin",
              "href" => "/admin/apps.php?app=$AppID"
            ),
            "app_disable" => array(
              "text" => "Disable",
              "href" => "/admin/apps.php?action=disable&app=$AppID" . \H::csrf("g")
            ),
            "app_remove" => array(
              "text" => "Remove",
              "href" => "/admin/apps.php?action=remove&app=$AppID" . \H::csrf("g")
            )
          ),
          "position" => "left"
        ));
        $pageResponse = $class->page($page);
        
        if($pageResponse === "auto"){
          if($page === "/"){
            $page = "/index";
          }
          $html = $class->inc("/src/page{$page}.php");
          if($html)
            Response::setPage($html);
          else
            ser();
        }else{
          if($pageResponse === null){
            ser();
          }else{
            Response::setPage($pageResponse);
          }
        }
      }else{
        echo ser();
      }
    });
    
    /**
     * Dashboard Page
     * The main Page. Add CSS & JS accordingly
     */
    self::route("/", function() {
      \Lobby::setTitle("Dashboard");
      \Lobby\UI\Themes::loadDashboard("head");
      Response::loadPage("/includes/lib/lobby/inc/dashboard.php");
    });
    
    /**
     * App Admin Page
     */
    self::route("/admin/app/[:appID]?/[**:page]?", function($request){
      $AppID = $request->appID;
      $page = $request->page != "" ? "/admin/{$request->page}" : "/admin/index";

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
        \Lobby::setTitle($AppInfo['name']);
        
        /**
         * Add the App item to the navbar
         */
        \Lobby\UI\Panel::addTopItem("lobbyApp{$AppID}", array(
          "text" => "Admin > " . $AppInfo['name'],
          "href" => "/admin/app/$AppID",
          "position" => "left",
          "subItems" => array(
            "gotoapp" => array(
              "text" => "Go To App",
              "href" => "/app/$AppID"
            )
          )
        ));
        
        $page_response = $class->page($page);
        if($page_response == "auto"){
          if($page === "/"){
            $page = "/index";
          }
          $GLOBALS['workspaceHTML'] = $class->inc("/src/page{$page}.php");
        }else{
          $GLOBALS['workspaceHTML'] = $page_response;
        }
        
        if($GLOBALS['workspaceHTML'] === false || $GLOBALS['workspaceHTML'] == null){
          ob_start();
            echo ser("Error", "The app '<strong>{$AppID}</strong>' does not have an Admin Page. <a clear href='". \Lobby::u("/app/$AppID") ."' class='btn green'>Go To App</a>");
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
