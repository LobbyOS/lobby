<?php
require_once __DIR__ . "/load.php";
$GLOBALS['workspaceHTML'] = "";

/**
 * Dispatch the Routes
 */
\Lobby\Router::dispatch();

if(!isset($GLOBALS['route_active'])){
  if($GLOBALS['workspaceHTML'] != "" || is_array($GLOBALS['workspaceHTML'])){
    require_once L_DIR . "/includes/lib/core/Inc/page.php";
  }else{
    ser();
  }
}
?>
