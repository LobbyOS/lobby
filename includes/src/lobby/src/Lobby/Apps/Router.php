<?php
/**
 * Lobby\Apps\Router
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/Apps/Router.php
 */

namespace Lobby\Apps;

use Klein\Klein;
use Klein\Request;
use Lobby\App;
use Response;

/**
 * Routing in app
 */
class Router {

  /**
   * App object
   * @var App
   */
  private $app;

  /**
   * Klein Router object
   * @var Klein
   */
  private $router;

  /**
   * Initialize
   * @param App $App App object
   */
  public function __construct(App $App){
    $this->app = $App;
    $this->router = new Klein();
  }

  /**
   * Route a URI
   * @param  [type] $route    Route URI
   * @param  [type] $callback Callback to call when route is matched
   */
  public function route($route, $callback) {
    $app = $this->app;
    $this->router->respond($route, function($request) use($app, $callback){
      return call_user_func_array($callback, array($app, $request));
    });
  }

  /**
   * Parse the routes and return response content
   */
  public function dispatch(){
    $request = Request::createFromGlobals();
    $uri = $request->server()->get('REQUEST_URI');

    /**
     * Set the request URI without the "/app/ID" part in it
     */
    $request->server()->set('REQUEST_URI', substr($uri, strlen("/app/{$this->app->id}")));

    return $this->router->dispatch($request, null, false, Klein::DISPATCH_CAPTURE_AND_RETURN);
  }

}
