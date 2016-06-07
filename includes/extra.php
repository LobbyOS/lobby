<?php
/**
 * Make important locations and URLs as 
 * constants to easily access them
 */
define("L_URL", \Lobby::getURL());

define("APPS_URL", L_URL . "/contents/apps");
define("THEMES_URL", L_URL . "/contents/themes");

/**
 * LOAD MODULES
 * ------------
 * It will first, load the core modules
 * Then the custom modules
 */
\Lobby\Modules::load();
