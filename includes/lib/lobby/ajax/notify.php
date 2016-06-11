<?php
$notifications = Lobby\DB::getJSONOption("notify_items");

/**
 * If there is a update available either app or core, add an 
 * "Update Available" icon on the right side of panel
 */
$AppUpdates = json_decode(Lobby\DB::getOption("app_updates"), true);
$lobby_version = \Lobby::$version;
$latestVersion = Lobby\DB::getOption("lobby_latest_version");

if((count($AppUpdates) != 0) || ($latestVersion && $lobby_version != $latestVersion)){
  $notifications["update"] = array(
    "contents" => "New Updates Are Available",
    "icon" => "update",
    "iconURL" => null,
    "href" => \Lobby::u("/admin/update.php")
  );
}

echo json_encode($notifications);
