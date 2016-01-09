<div class="contents">
  <h2>sige</h2>
  <p>Change the settings of the site <strong><?php echo $name;?></strong></p>
  <?php
  if(isset($_POST['submit'])){
    $output = $_POST['output'];
    $tagline = $_POST['tagline'];
    $theme = isset($_POST['theme']) ? $_POST['theme'] : "";
    if($output != "" && $theme != ""){
      if(!file_exists($output) || !is_writable($output)){
        \Lobby::ser("Output Path problem", "The path you gave as output doesn't exist or permission is not acceptable. Make sure it's an existing directory with Read & Write permission", false);
      }else if(array_search($theme, $this->themes) === false){
        \Lobby::ser("Invalid Theme", "The theme you selected doesn't exist", false);
      }else{
        // Everything's great
        $this->addSite($name, $tagline, $output, $theme, (isset($_POST['empty']) ? 1 : 0), (isset($_POST['titleTag']) ? 1 : 0));
        
        /* Generate the site */
        $gSite = new \Lobby\App\sige\Site($this->getSite($name), $this);
        $gSite->generate($this->getPages($name));
        \Lobby::sss("Site updated", "The site was updated and generated successfully");
      }
    }else{
      \Lobby::ser("Fill Up", "Please fill the form completely", false);
    }
  }
  ?>
  <form action="<?php echo \Lobby::u();?>" method="POST" clear>
    <?php
    $site = $this->getSite($name);
    $empty = $site['empty'] == 1 ? "checked='checked'" : "";
    $titleTag = isset($site['titleTag']) && $site['titleTag'] == 1 ? "checked='checked'" : "";
    if($site !== false){
    ?>
      <label>
        <div>Tagline</div>
        <input type="text" name="tagline" title="The tagline of site" value="<?php echo $site['tagline'];?>" />
      </label><cl/>
      <label>
        <div>Output location</div>
        <input type="text" name="output" title="Where the generated site should be extracted" value="<?php echo $site['out'];?>" />
      </label><cl/>
      <label>
        <div>Empty Output location</div>
        <input type="checkbox" name="empty" title="Should the contents of output directory be removed before making the site" <?php echo $empty;?>/>
      </label>
      <label>
        <div>Append Site Name to &lt;title&gt; tag</div>
        <input type="checkbox" name="titleTag" title="Append the site name after page title ? Example : 'My Page - Delicious Blog'" <?php echo $titleTag;?>/>
      </label>
      <div>
        <div>Theme</div>
        <?php
        foreach($this->themes as $theme){
          $checked = $site['theme'] == $theme ? "checked" : "false";
        ?>
          <label class='theme'>
            <a title="Click to see example" target="_blank" href="<?php echo $this->u("/src/data/themes/{$theme}/example.html");?>">
              <img src="<?php echo APP_SRC . "/src/data/themes/{$theme}/thumbnail.png";?>" />
            </a>
            <input type="radio" name="theme" value="<?php echo $theme;?>" checked="<?php echo $checked;?>" />
          </label>
        <?php
        }
        ?>
      </div>
    <?php
    }
    ?>
    <div clear>
      <button name="submit">Update Settings</button>
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
