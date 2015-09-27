<div class="contents">
  <h2>sige</h2>
  <p>Manage Pages of site <strong><?php echo $name;?></strong></p>
  <p>
    <?php
    echo \Lobby::l("$su/edit", "New Page", "class='button'");
    
    $pages = $this->getPages($name);
    if(count($pages) == 0){
      \Lobby::ser("No Pages", "No pages has been created.");
      echo '<p><strong>Note that a page called "index" should be created in the site.</strong></p>';
    }else{
      echo "<h3>Pages</h3>";
      foreach($pages as $id => $page){
        echo \Lobby::l( "{$su}/edit?id=$id", "$id", "class='button'" ) . "<cl/>";
      }
    }
    ?>
  </p>
</div>