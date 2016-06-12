<?php
$offset = Request::postParam("offset");
if($offset !== null){
  $timeZone = \Lobby\Time::getTimezone($offset);
  if(@date_default_timezone_set($timeZone))
    \Lobby\DB::saveOption("lobby_timezone", $timeZone);
}
?>
