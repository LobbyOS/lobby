<?php
if(isset($dbinfo)){
  require_once APP_DIR . "/src/inc/class.logsys.php";

  \Lobby\App\fr_logsys\Fr\LS::config(array(
    "db" => array(
      "host" => $dbinfo['db_host'],
      "port" => $dbinfo['db_port'],
      "username" => $dbinfo['db_username'],
      "password" => $dbinfo['db_password'],
      "name" => $dbinfo['db_name'],
      "table" => $dbinfo['db_table']
    ),
    "features" => array(
      "start_session" => false
    )
  ));
}
