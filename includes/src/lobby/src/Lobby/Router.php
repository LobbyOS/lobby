<?php
/**
 * Lobby\Router
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/Router.php
 */

namespace Lobby;

use CSRF;
use Klein\Klein;
use Lobby\Apps;
use Lobby\DB;
use Request;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The Router class for Routing paths coming to Lobby accordingly
 * Klein is used as the Routing Library, so Route names and others
 * will be as Klein's : https://github.com/chriso/klein.php
 */
class Router {

  /**
   * Klein router object
   * @var Klein
   */
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

    /**
     * Handle AR
     */
    self::route("/lobby/ar/[*:handler]", function($request){
      if(!CSRF::check())
        return false;

      if($request->handler === "notify"){
        Response::loadContent("/includes/lib/lobby/ar/notify.php");
      }else if($request->handler === "save/option"){
        $key = Request::postParam("key");
        $value = Request::postParam("value");

        if(DB::saveOption($key, $value)){
          Response::setContent("1");
        }else{
          Response::setContent("0");
        }
      }else if($request->handler === "admin/enable-app"){
        Response::loadContent("/admin/ar/enable-app.php");
      }else if($request->handler === "admin/install-app"){
        Response::loadContent("/admin/ar/install-app.php");
      }else if($request->handler === "admin/set-timezone"){
        Response::loadContent("/admin/ar/set-timezone.php");
      }else if($request->handler === "filepicker"){
        Response::loadContent("/includes/lib/modules/filepicker/ar/filepicker.php");
      }
    });

    /**
     * Handle AR to apps
     */
    self::route("/lobby/ar/app/[s:appID]/[*:handler]", function($request){
      if(!CSRF::check())
        return false;

      $App = new Apps($request->appID);

      if($App->exists && $App->enabled){
        $AppObj = $App->getInstance();

        $key = Request::postParam("key");
        $value = Request::postParam("value");

        if($request->handler === "data/save"){
          if($key !== null && $value !== null){
            if(is_array($value)){
              $AppObj->data->saveArray($key, $value);
            }else{
              $AppObj->data->saveValue($key, $value);
            }
            Response::setContent("1");
          }
        }else if($request->handler === "/data/remove"){
          $AppObj->data->remove($key);
          Response::setContent("1");
        }else{
          $response = $AppObj->getARResponse($request->handler);

          if($response !== false){
            /**
             * Response shouldn't be empty
             */
            Response::setContent($response == null ? "1" : $response);
          }else{
            /**
             * AR request was invalid
             */
            Response::showError();
          }
        }
      }
    });
  }

  /**
   * Normalize a path string
   * @param  string $path Path to normalize
   * @return string       Normalized path
   */
  private static function getAbsolutePath($path) {
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
      if ('.' == $part) continue;
      if ('..' == $part) {
        array_pop($absolutes);
      } else {
        $absolutes[] = $part;
      }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
  }

  /**
   * Make HTTP requested path to absolute location
   * @param string $path Requested file path
   * @return bool Whether the request points to a valid file
   */
  private static function getServeFileAbsolutePath($path){
    $path = FS::loc(self::getAbsolutePath($path));

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
