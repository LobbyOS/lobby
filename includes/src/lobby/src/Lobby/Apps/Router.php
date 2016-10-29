<?php
namespace Lobby\Apps;

use Klein\Klein;
use Klein\Request;
use Lobby\App;
use Response;

/**
 * Routing in app
 */
class Router {

  private $app;
  private $router;

  public function __construct(App $App){
    $this->app = $App;
    $this->router = new Klein();
  }

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
