<?php
$this->setTitle("Site $name");
?>
<div class="contents">
  <h2><?php echo $name;?></h2>
  <p>About yout site.</p>
  <p><strong>Note that a page called "index" should be created in the site.</strong></p>
  <p clear>
    <?php echo \Lobby::l("$su/settings", "Settings", "class='button'");?>
    <?php echo \Lobby::l("$su/pages", "Pages", "class='button'");?>
  </p>
  <form clear method="POST" action="<?php echo \Lobby::u();?>">
    <button style="font-size: 25px;" name="generate" class="button orange">Generate Site NOW!</button>
  </form>
  <?php
  if(isset($_POST['generate'])){
    /* Generate the site */
    $gSite = new \Lobby\App\sige\Site($this->getSite($name), $this);
    $gSite->generate($this->getPages($name));
    \Lobby::sss("Generated Site", "The site was successfully generated");
  }
  ?>
</div>
