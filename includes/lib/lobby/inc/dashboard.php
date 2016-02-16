<?php
/**
 * Get installed apps and make the tiles on dashboard
 */
$apps = \Lobby\Apps::getEnabledApps();
if(count($apps) == 0){
  ser("No Apps", "You haven't enabled or installed any apps. <br/>Get great Apps from " . \Lobby::l("/admin/lobby-store.php", "Lobby Store"));
}else{
  $dashboard_items = array(
    "apps" => array()
  );
  foreach($apps as $app => $null){
    $App = new \Lobby\Apps($app);
    $data = $App->info;
    $dashboard_items["apps"][$app] = $data;
  }
  \Lobby\UI\Themes::loadDashboard($dashboard_items);
}
