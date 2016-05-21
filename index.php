<?php
/**
 *
 * index.php file for LobbyOS
 *
 * A localhost/Web OS For Web Apps: http://lobby.subinsb.com
 *
 * @category   lobby
 * @package    lobby
 * @author     The LobbyOS developer community
 * @license    Apache License
 * @version    0.2.1
 */

require_once __DIR__ . "/load.php";
use \Lobby\Router;

/**
 * Dispatch the Routes
 */
Router::dispatch();

if(!Router::$routeActive){
  if(Response::hasContent()){
    Response::send();
  }else if(Router::pathExists()){
    return false;
  }else{
    ser();
  }
}
