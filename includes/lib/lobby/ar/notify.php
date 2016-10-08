<?php
$notifications = Lobby\DB::getJSONOption("notify_items");

/**
 * If there is a update available either app or core, add a Notify item
 */
if(\Lobby\Update::isAvailable()){
  $notifications["update"] = array(
    "contents" => "New Updates Are Available",
    "icon" => "update",
    "iconURL" => null,
    "href" => \Lobby::u("/admin/update.php")
  );
}

echo json_encode($notifications);
