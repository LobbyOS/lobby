<?php
class logSysLobbyDB {
  public function prepare($query){
    $obj = \Lobby\DB::$dbh->prepare($query);
    return $obj;
  }
}
require_once __DIR__ . "/class.logsys.php";

$salt = getOption("admin_secure_salt");
$cookie = getOption("admin_secure_cookie");

\Fr\LS::$config = array(
  "db" => array(
    "table" => \Lobby\DB::$prefix . "users"
  ),
  "features" => array(
    "auto_init" => false,
    "start_session" => false,
    "email_login" => false,
  ),
  "keys" => array(
    "cookie" => $cookie,
    "salt" => $salt
  ),
  "pages" => array(
    "no_login" => array(),
    "login_page" => "/admin/login",
    "home_page" => "/admin/"
  )
);
\Fr\LS::construct();
