<div class="side-nav fixed" id="slide-out">
  <a target="_blank" href="http://lobby.subinsb.com" class="lobby-link">Lobby <?php echo \Lobby::getVersion(true);?></a>
  <?php
  $links = array(
    "/admin/index.php" => "Dashboard",
    "/admin/apps.php" => "Apps",
    "/admin/lobby-store.php" => "Lobby Store",
    "/admin/modules.php" => "Modules",
    "/admin/settings.php" => "Settings",
  );
  $links = Hooks::applyFilters("admin.view.sidebar", $links);
  
  $curPage = \Lobby::curPage();
  foreach($links as $link => $text){
    if(substr($curPage, 0, strlen($link)) === $link || ($curPage == "/admin/update.php" && $text == "Settings") || ($curPage == "/admin/install-app.php" && $text == "Apps")){
      echo \Lobby::l($link, $text, "class='link active'");
    }else{
      echo \Lobby::l($link, $text, "class='link'");
    }
  }
  ?>
</div>
