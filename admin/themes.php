<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Response::head("App Manager");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div id="workspace">
      <div class="content">
        <h1>Apps</h1>
        <p>Disable or Remove installed apps. You can find and install more Apps from <a href="<?php echo L_URL;?>/admin/lobby-store.php">Lobby Store</a>.</p>
        <?php
        if(isset($_GET['action']) && isset($_GET['app']) && CSRF::check()){
          $action = $_GET['action'];
          $app = $_GET['app'];
          $App = new \Lobby\Apps($app);
          if( !$App->exists ){
            echo ser("Error", "I checked all over, but App does not Exist");
          }
          if($action == "disable"){
            if($App->disableApp()){
              echo sss("Disabled", "The App <strong>$app</strong> has been disabled.");
            }else{
              echo ser("Error", "The App <strong>$app</strong> couldn't be disabled. Try again.", false);
            }
          }else if($action == "remove"){
        ?>
            <h2>Confirm</h2>
            <p>Are you sure you want to remove the app <b><?php echo $app;?></b> ?</p>
            <div clear></div>
            <a class="btn green" href="<?php echo L_URL ."/admin/install-app.php?action=remove&id={$app}&".CSRF::getParam();?>">Yes, I'm Sure</a>
            <a class="btn red" href="<?php echo L_URL ."/admin/apps.php";?>">No, I'm Not</a>
        <?php
            exit;
          }else if($action == "enable"){
            if($App->enableApp()){
              echo sss("Enabled", "App has been enabled.");
            }else{
              echo ser("Error", "The App couldn't be enabled. Try again.", false);
            }
          }
        }
        $Apps = \Lobby\Apps::getApps();
    
        if(count($Apps) == 0){
          echo ser("No Enabled Apps", "Lobby didn't find any apps that has been enabled", false);
        }
        if(count($Apps) != 0){
        ?>
          <table style="width: 100%;margin-top:5px">
            <thead>
              <tr>
                <td>Name</td>
                <td>Version</td>
                <td>Description</td>
                <td>Actions</td>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach($Apps as $app => $null){
                $App = new \Lobby\Apps($app);
                $data = $App->info;
                $appImage = !isset($data['image']) ? L_URL . "/includes/lib/lobby/image/blank.png" : $data['image'];
                $enabled = $App->enabled;
              ?>
                <tr <?php if(!$enabled){echo 'style="background: #EEE;"';}?>>
                  <td>
                    <a href="<?php echo \Lobby::u("/admin/app/$app");?>"><?php echo $data['name'];?></a>
                  </td>
                  <td><?php echo $data['version'];?></td>
                  <td><?php echo $data['short_description'];?></td>
                  <td style="//text-align:center;">
                    <?php
                    if($enabled){
                      echo '<a class="btn" href="?action=disable&app='. $app . CSRF::getParam() .'">Disable</a>';
                    }else{
                      echo '<a class="btn" href="?action=enable&app='. $app . CSRF::getParam() .'">Enable</a>';
                    }
                    ?>
                    <a class="btn red" href="?action=remove&app=<?php echo $app . CSRF::getParam();?>">Remove</a>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        <?php
        }
        ?>
      </div>
    </div>
  </body>
</html>
