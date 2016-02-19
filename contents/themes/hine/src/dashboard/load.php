<?php
/**
 * Get installed apps and make the tiles on dashboard
 */
$apps = \Lobby\Apps::getEnabledApps();

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
  $appsSorted = array();
  foreach($apps as $app){
    $App = new \Lobby\Apps($app);
    $data = $App->info;
    $lowercased_name = strtolower($data['name']);
    $appsSorted[$lowercased_name] = $data;
  }
  /**
   * Ascending order
   */
  ksort($appsSorted);
  
  foreach($appsSorted as $data){
    $app = $data['id'];
    $jsCode .= "lobby.dash.addTile('app', {'id' : '{$app}', 'img' : '{$data['logo']}', 'name' : '{$data['name']}'});";
  }
  
  /**
   * A call to create Dashboard
   */
  $jsCode .= "lobby.dash.init();";
  echo "<script>lobby.load(function(){ $jsCode });</script>";
  echo "<div class='tiles'></div>";
}
