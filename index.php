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

/**
 * Dispatch the Routes
 */
return \Lobby\Router::dispatch();
