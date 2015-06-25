<?php
/**
 * The online server of Lobby.
 * Default : http://lobby.subinsb.com/api
 */
define("L_SERVER", "http://lobby.subinsb.com/api");

/**
 * Make important locations and URLs as constants
 * to easily access them
 */
define("L_URL", \Lobby::$host);
define("APPS_URL", L_URL . "/contents/apps");
define("APPS_DIR", \Lobby\FS::loc("/contents/apps"));

/**
 * LOAD MODULES
 * ------------
 * It will : First, load the core modules
 * Then the custom modules
 */
require_once \Lobby\FS::loc("/includes/src/Modules.php");
\Lobby\Modules::load();
