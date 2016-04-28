<?php
/**
 * The online server of Lobby.
 * Default : http://lobby.subinsb.com
 */
define("L_SERVER", "http://server.lobby.sim");

/**
 * Make important locations and URLs as 
 * constants to easily access them
 */
define("L_URL", \Lobby::$url);
define("APPS_DIR", L_DIR . "/contents/apps");
define("APPS_URL", L_URL . "/contents/apps");
define("THEMES_DIR", L_DIR . "/contents/themes");
define("THEMES_URL", L_URL . "/contents/themes");

/**
 * LOAD MODULES
 * ------------
 * It will first, load the core modules
 * Then the custom modules
 */
\Lobby\Modules::load();
