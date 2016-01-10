<?php
/**
 * Get installed apps and make the tiles on dashboard
 */
$apps = \Lobby\Apps::getEnabledApps();
/**
 * Ascending order
 */
asort($apps);

if(count($apps) == 0){
  ser("No Apps", "You haven't enabled or installed any apps. <br/>Get great Apps from " . \Lobby::l("/admin/lobby-store.php", "Lobby Store"));
}else{
  $jsCode = "";
  /**
   * $dashItems contains the positions of the tiles
   * set by the user before
   */
  $dashItems = getOption("dashItems");
  if($dashItems != null){
    $jsCode .= "lobby.dash.data = ". $dashItems .";";
  }
  foreach($apps as $app){
    $App = new \Lobby\Apps($app);
    $data = $App->info;
    $jsCode .= "lobby.dash.addTile('app', {'id' : '{$app}', 'img' : '{$data['logo']}', 'name' : '{$data['name']}'});";
  }
  /**
   * A call to create Dashboard
   */
  $jsCode .= "lobby.dash.init();";
  echo "<script>lobby.load(function(){ $jsCode });</script>";
  echo "<div class='tiles'></div>";
}
