<div class="sidebar">
  <div style="height:32px;text-align:center;margin-top:10px;">
    <a target="_blank" href="http://lobby.subinsb.com" style="color:white;">Lobby <?php echo getOption("lobby_version");?></a>
  </div>
  <?php
  $links = array(
    "/admin" => "Dashboard",
    "/admin/apps.php" => "Apps",
    "/admin/lobby-store.php" => "Lobby Store",
    "/admin/modules.php" => "Modules",
    "/admin/settings.php" => "Settings",
  );
  $curPage = \Lobby::curPage();
  foreach($links as $link => $text){
    if($link == $curPage || ($curPage == "/admin/update.php" && $text == "Settings") || ($curPage == "/admin/install-app.php" && $text == "Apps")){
      echo \Lobby::l($link, $text, "class='link active'");
    }else{
      echo \Lobby::l($link, $text, "class='link'");
    }
  }
  ?>
</div>
