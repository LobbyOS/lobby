<div class="contents">
  <h2>Static Site Generator (SIGE) </h2>
  <?php echo \Lobby::l($this->u("/new"), "New Site", "class='button'");?>
  <p>
    <?php
    $sites = getData("sites");
    if($sites !== false){
      echo "<h2>Your Sites</h2>";
      $sites = json_decode($sites, true);
      foreach($sites as $name => $site){
        echo $this->l("/site/". urlencode($name), $name, "class='button green'") . "<cl/>";
      }
    }
    ?>
  </p>
</div>
