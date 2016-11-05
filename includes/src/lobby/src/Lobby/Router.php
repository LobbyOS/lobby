<?php
/**
 * Lobby\Router
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/Router.php
 */

namespace Lobby;

use Klein\Klein;
use Request;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    }else if(self::serveFile()){
      return true;
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
   * Make HTTP requested path to absolute location
   * @param string $path Requested file path
   * @return bool Whether the request points to a valid file
   */
  private static function getServeFileAbsolutePath($path){
    $path = realpath(L_DIR . $path);

    if(file_exists($path)){
      // Folder index
      if(is_dir($path)){
        $path .= "/index.php";
      }

      if(file_exists($path) && substr($path, 0, strlen(L_DIR)) === L_DIR){
        return $path;
      }
    }
    return false;
  }

  /**
   * Process & serve files
   * @return bool Whether a file was served
   */
  public static function serveFile(){
    $path = self::getServeFileAbsolutePath(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
    $pathInfo = pathinfo(FS::rel($path));

    if($path){
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $type = finfo_file($finfo, $path);
      finfo_close($finfo);

      header("Cache-Control: public");

      if($type === "text/x-php" || $type === "text/html"){
        /**
         * Do not let access to PHP files inside contents & includes directory
         * except for "includes/serve-assets.php" file
         */
        if(substr($pathInfo["dirname"], 0, 8) === "contents" || (substr($pathInfo["dirname"], 0, 8) === "includes" && $pathInfo["filename"] !== "serve-assets"))
          return false;

        $content = Response::getFile($path);

        Response::setContent($content);
        Response::setCache(array(
          "etag" => md5($content),
          "public" => true
        ));
        Response::send();

        return true;
      }else{
        $request = Request::getRequestObject();
        $response = new BinaryFileResponse($path, 200, array(), true, null, true);

        /**
         * For SVG images, we check the extension
         */
        if($pathInfo["extension"] === "svg"){
          $response->headers->set("Content-type", "image/svg+xml");
        }

        if($response->isNotModified($request)){
          $response->setStatusCode(304);
          $response->prepare($request);
          $response->send();
        }else{
          $response->prepare($request);
          $response->send();
        }
        return true;
      }
    }
    return false;
  }

}
