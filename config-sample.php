<?php
/**
 * A Sample Configuration File
 * ---------------------------
 * If you're doing manual installation, you can obtain lobbyID & secureID from
 * http://lobby.subinsb.com/api/lobby/installation-id
 *
 */
return array(
  'db' => array(
    'host' => '127.0.0.1',
    'port' => '3306',
    'username' => '',
    'password' => '',
    'dbname' => '',
    'prefix' => 'l_'
  ),
  /**
   * The GLOBAL unique ID of YOUR Lobby installation
   */
  'lobbyID' => '',
  /**
   * A Secure Identity of YOUR Lobby Installation.
   * Never reveal it TO ANYONE
   */
  'secureID' => '',
  'debug' => false
);
?>
