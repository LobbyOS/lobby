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

/**
 * Some checking to make sure Lobby works fine
 */
if(!Lobby::status("lobby.assets-serve")){
  if(!is_writable(L_DIR)){
    $error = array("Fatal Error", "The permissions of the Lobby folder is invalid. You should change the permission of <blockquote>". L_DIR ."</blockquote>to read and write (0755).");

    if(Lobby::getSysInfo("os") === "linux"){
      $error[1] .= "<p clear>On Linux systems, do this in terminal : <blockquote>sudo chown \${USER}:www-data ". L_DIR ." -R && sudo chmod u+rwx,g+rw,o+r ". L_DIR ." -R</blockquote></p>";
    }
    Response::showError($error[0], $error[1]);
  }
}
