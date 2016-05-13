<?php
/**
 * Get installed apps and make the tiles on dashboard
 */
$apps = \Lobby\Apps::getEnabledApps();

if(count($apps) == 0){
  ser("No Apps", "You haven't enabled or installed any apps. <br/>Get great Apps from " . \Lobby::l("/admin/lobby-store.php", "Lobby Store"));
}else{
  $jsCode = "";
  
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
    $data['logo'] = $data['logo'] === null ? THEME_URL . "/src/dashboard/image/blank.png" : $data['logo'];
    $jsCode .= "lobby.dash.addTile({'id' : '{$app}', 'img' : '{$data['logo']}', 'name' : '{$data['name']}'});";
  }
  
  /**
   * A call to create Dashboard
   */
  $jsCode .= "lobby.dash.init();";
  echo "<script>lobby.load(function(){ $jsCode });</script>";
  echo "<ul class='tiles-wrapper' data-intro='Your installed apps will be shown here in the Dashboard'><li class='tiles' data-page='0' active></li></ul>";
  echo "<div id='dash-control'><ul class='tabs' id='bx-pager'></ul></div>";
}
