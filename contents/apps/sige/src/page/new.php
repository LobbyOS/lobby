<div class="contents">
  <h2>New Site</h2>
  <?php
  if(isset($_POST['submit'])){
    $name = $_POST['site'];
    $output = $_POST['output'];
    $theme = isset($_POST['theme']) ? $_POST['theme'] : "";
    /* Any change to the below must be reflected in settings.php */
    if($name != "" && $output != "" && $theme != ""){
      if(!file_exists($output) || !is_writable($output)){
        \Lobby::ser("Output Path problem", "The path you gave as output doesn't exist or permission is not acceptable. Make sure it's an existing directory with Read & Write permission", false);
      }else if(!ctype_alnum(str_replace(" ", "", $name))){
        \Lobby::ser("Invalid Name", "Only alphanumeric characters are allowed for Site Name", false);
      }else if(array_search($theme, $this->themes) === false){
        \Lobby::ser("Invalid Theme", "The theme you selected doesn't exist", false);
      }else{
        // Everything's great
        $this->addSite($name, $output, $theme);
        \Lobby::sss("Site added", "The site was added successfully");
      }
    }else{
      \Lobby::ser("Fill Up", "Please fill the form completely", false);
    }
  }
  ?>
  <form action="" method="POST">
    <label>
      <div>Site Name</div>
      <input type="text" name="site" />
    </label><cl/>
    <label>
      <div>Output Location</div>
      <input type="text" name="output" title="Where the generated site should be extracted" />
    </label><cl/>
    <label>
      <div>Empty Output location</div>
      <input type="checkbox" name="empty" title="Should the contents of output directory be removed before generating the site everytime" />
    </label>
    <div>
      <div>Theme</div>
      <?php
      foreach($this->themes as $theme){
      ?>
        <label class='theme'>
          <a target="_blank" href="<?php echo $this->u("/src/data/themes/{$theme}/example.html");?>">
            <img src="<?php echo $this->u("/src/data/themes/{$theme}/thumbnail.png");?>" />
          </a>
          <input type="radio" name="theme" value="<?php echo $theme;?>" />
        </label>
      <?php
      }
      ?>
    </div>
    <div clear>
      <button name="submit">Create Site</button>
    </div>
  </form>
  <style>
    .workspace#sige label img{
      width: 126px;
      height: 96px;
    }
    .workspace#sige label.theme{
      width: 126px;
      height: 96px;
      position: relative;
    }
    .workspace#sige label.theme input{
      position: absolute;
      left: 4px;
      right: 0px;
      bottom: 6px;
      margin: 0px;
      background: black;
    }
  </style>
</div>
