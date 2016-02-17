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
$GLOBALS['workspaceHTML'] = "";

/**
 * Dispatch the Routes
 */
\Lobby\Router::dispatch();

if(!isset($GLOBALS['route_active'])){
  if($GLOBALS['workspaceHTML'] != "" || is_array($GLOBALS['workspaceHTML'])){
    require_once L_DIR . "/includes/lib/lobby/inc/page.php";
  }else if(\Lobby\Router::pathExists()){
    return false;
  }else{
    ser();
  }
}
