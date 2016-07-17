<?php
/**
 * Make important locations and URLs as 
 * constants to easily access them
 */
define("L_URL", \Lobby::getURL());

define("THEME_ID", Lobby\UI\Themes::getThemeID());
define("THEME_DIR", Lobby\UI\Themes::getThemeDir());
define("THEME_URL", Lobby\UI\Themes::getThemeURL());

/**
 * LOAD MODULES
 * ------------
 * It will first, load the core modules
 * Then the custom modules
 */
\Lobby\Modules::load();
