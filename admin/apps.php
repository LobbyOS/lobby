<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Lobby::addScript("admin.apps.js", "/admin/js/apps.js");
    \Lobby::head("App Manager");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <h1>Apps</h1>
        <p>Disable or Remove installed apps. You can find and install more Apps from <a href="<?php echo L_URL;?>/admin/lobby-store.php">Lobby Store</a>.</p>
        <?php
        if(isset($_GET['action']) && isset($_GET['app']) && H::csrf()){
          $action = $_GET['action'];
          $apps = $_GET['app'];
          
          /**
           * If only a single app, make it as a value of array
           */
          if(!is_array($apps)){
            $apps = array($apps);
          }
          
          foreach($apps as $app){
            $App = new \Lobby\Apps($app);
            if( !$App->exists ){
              ser("Error", "I checked all over, but App does not Exist");
            }
            if($action == "disable"){
              if($App->disableApp()){
                sss("Disabled", "The App <strong>$app</strong> has been disabled.");
              }else{
                ser("Error", "The App <strong>$app</strong> couldn't be disabled. Try again.", false);
              }
            }else if($action == "remove"){
          ?>
              <h2>Confirm</h2>
              <p>Are you sure you want to remove the app <b><?php echo $app;?></b> ?</p>
              <div clear></div>
              <a class="button green" href="<?php echo L_URL ."/admin/install-app.php?action=remove&id={$app}&".H::csrf("g");?>">Yes, I'm Sure</a>
              <a class="button red" href="<?php echo L_URL ."/admin/apps.php";?>">No, I'm Not</a>
          <?php
              exit;
            }else if($action == "enable"){
              if($App->enableApp()){
                if(isset($_GET['redirect'])){
                  \Lobby::redirect("/app/$app");
                }
                sss("Enabled", "The App <strong>$app</strong> has been enabled.");
              }else{
                ser("Error", "The App couldn't be enabled. Try again.", false);
              }
            }
          }
        }
        $Apps = \Lobby\Apps::getApps();
    
        if(count($Apps) == 0){
          ser("No Enabled Apps", "Lobby didn't find any apps", false);
        }
        if(count($Apps) != 0){
        ?>
          <form>
            <table style="width: 100%;margin-top:5px" id="apps_table">
              <thead>
                <tr>
                  <td>
                    <label><input type="checkbox" id="select_all_apps" /><span></span></label>
                  </td>
                  <td>Name</td>
                  <td>Version</td>
                  <td>Description</td>
                  <td>Actions</td>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach($Apps as $app){
                  $App = new \Lobby\Apps($app);
                  $data = $App->info;
                  $appImage = !isset($data['image']) ? L_URL . "/includes/lib/lobby/image/blank.png" : $data['image'];
                  $enabled = $App->isEnabled();
                ?>
                  <tr <?php if(!$enabled){echo 'style="background: #EEE;"';}?>>
                    <td>
                      <label>
                        <input type="checkbox" name="app[]" value="<?php echo $app;?>" id="checkbox-app" />
                        <span></span>
                      </label>
                    </td>
                    <td>
                      <a href="<?php echo \Lobby::u("/admin/app/$app");?>"><?php echo $data['name'];?></a>
                    </td>
                    <td><?php echo $data['version'];?></td>
                    <td><?php echo $data['short_description'];?></td>
                    <td style="text-align:center;">
                      <?php
                      if($enabled){
                        echo '<a class="button" href="?action=disable&app='. $app . H::csrf('g') .'">Disable</a>';
                      }else{
                        echo '<a class="button" href="?action=enable&app='. $app . H::csrf('g') .'">Enable</a>';
                      }
                      ?>
                      <a class="button red" href="?action=remove&app=<?php echo $app . H::csrf('g');?>">Remove</a>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
            <div id="combined_actions">
              <span style="padding-left: 20px;">^</span>
              <button class="button green" name="action" value="enable">Enable</button>
              <button class="button blue" name="action" value="disable">Disable</button>
            </div>
            <?php echo H::csrf('i');?>
          </form>
        <?php
        }
        ?>
      </div>
    </div>
  </body>
</html>
