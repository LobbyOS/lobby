<?php
require "../load.php";
use \Lobby\Apps;
use \Lobby\FS;
use \Lobby\Need;
?>
<html>
  <head>
    <?php
    \Assets::js("admin.apps.js", "/admin/js/apps.js");
    \Assets::css("lobby-store", "/admin/css/lobby-store.css");
    \Assets::css("view-app", "/admin/css/view-app.css");
    
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
      <div class="contents">
        <?php
        $appID = Request::get("app");
        if($appID !== null){
          $App = new Apps($appID);
          
          if( !$App->exists ){
            echo ser("Error", "I checked all over, but App does not Exist");
          }
        ?>
          <h2><?php echo "<a href='". L_SERVER ."/apps/". $App->info['id'] ."' target='_blank'>". $App->info['name'] ."</a>";?></h2>
          <p class="chip" style="margin: -5px 0 20px;"><?php echo $App->info['short_description'];?></p>
          <?php
          if(isset($_GET['action']) && CSRF::check()){
            $action = $_GET['action'];
            
            if($action === "disable"){
              if($App->disableApp()){
                echo sss("Disabled", "The App <strong>$app</strong> has been disabled.");
              }else{
                echo ser("Error", "The App <strong>$app</strong> couldn't be disabled. Try again.", false);
              }
            }else if($action === "remove"){
            ?>
                <h2>Confirm</h2>
                <p>Are you sure you want to remove the app <b><?php echo $app;?></b> ?</p>
                <div clear></div>
                <a class="btn green" href="<?php echo L_URL ."/admin/install-app.php?action=remove&id={$app}&".CSRF::getParam();?>">Yes, I'm Sure</a>
                <a class="btn red" href="<?php echo L_URL ."/admin/apps.php";?>">No, I'm Not</a>
            <?php
              exit;
            }else if($action === "enable"){
              if($App->enableApp()){
                if(isset($_GET['redirect'])){
                  \Response::redirect("/app/$appID");
                }
                echo sss("Enabled", "The App <strong>$appID</strong> has been enabled.");
              }else{
                echo ser("Error", "The App couldn't be enabled. Try again.", false);
              }
            }
          }
          ?>
          <div class="row">
            <div class="col m3" id="leftpane" style="text-align: center;">
              <img src="<?php echo \Lobby::u("admin/image/clear.gif");?>" height="200" width="200" />
              <script>
                $(window).load(function(){
                  var image = $("#leftpane img");
                  var downloadingImage = new Image();
                  downloadingImage.onload = function(){
                    image.attr("src", this.src);
                  };
                  downloadingImage.src = "<?php echo $App->info["logo"];?>";
                });
              </script>
              <?php
              $App = new Apps($appID);
              $requires = $App->info['require'];
              
              if($App->hasUpdate()){
                /**
                 * New version of app is available
                 */
                echo \Lobby::l("/admin/check-updates.php", "Update App", "class='btn red'");
              }else if($App->enabled){
                echo \Lobby::l($App->info['url'], "Open App", "class='btn green'");
                echo \Lobby::l("/admin/apps.php?app=$appID&action=disable" . CSRF::getParam(), "Disable", "class='btn'");
              }else{
                /**
                 * App is Disabled. Show button to enable it
                 */
                echo \Lobby::l("/admin/apps.php?action=enable&redirect=1&app=". $appID . CSRF::getParam(), "Enable", "class='btn green'");
              }
              echo \Lobby::l("/admin/apps.php?app=$appID&action=remove" . CSRF::getParam(), "Remove", "class='btn red'");
              ?>
            </div>
            <div class="col m9">
              <ul class="tabs">
                <li class="tab"><a href="#app-info">Info</a></li>
                <li class="tab"><a href="#app-data">Memory</a></li>
              </ul>
              <div id="app-info" class="tab-contents">
                <div class="chip">Version : <?php echo $App->info['version'];?></div><cl/>
                <div class="chip">Developed By <a href="<?php echo $App->info['author_page'];?>" target="_blank"><?php echo $App->info['author'];?></a></div><cl/>
                <div class="chip"><a href="<?php echo $App->info['app_page'];?>" target="_blank">App's Webpage</a></div><cl/>
                <?php
                if(!empty($App->info["require"])){
                  $requirementsInSystemInfo = Need::checkRequirements($App->info["require"]);
                  echo "<div class='chip'>Requirements :</div><ul>";
                  foreach($App->info["require"] as $k => $v){
                    if($requirementsInSystemInfo[$k]){
                      echo "<li class='collection-item'>$k $v</li>";
                    }else{
                      echo "<li class='collection-item red'>$k $v</li>";
                    }
                  }
                  echo "</ul>";
                }
                ?>
            </div>
            <div id="app-data" class="tab-contents">
              <table>
                <tbody>
                  <tr>
                    <td>Installed in</td>
                    <td><?php echo $App->appDir;?></td>
                  </tr>
                  <tr>
                    <td>Folder</td>
                    <td><h6><?php $folderSize = FS::getSize($App->appDir);echo FS::normalizeSize($folderSize);?></h6></td>
                  </tr>
                  <tr>
                    <td title="Size occupied in database">App Data</td>
                    <td><h6><?php $dbSize = $App->getDBSize();echo FS::normalizeSize($dbSize);?></h6></td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td>Total size</td>
                    <td><h5><?php echo FS::normalizeSize($folderSize + $dbSize);?></h5></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <style>
          .tab-contents{
            padding: 10px 0;
          }
          </style>
        <?php
        }else{
        ?>
          <h2>Apps</h2>
          <p>Manage <b>installed apps</b>. You can find and install more Apps from <a href="<?php echo L_URL;?>/admin/lobby-store.php">Lobby Store</a>.</p>
        <?php
          $apps = Apps::getApps();
          
          if(empty($apps)){
            echo ser("No Apps", "You haven't installed any apps. <br/>Get great Apps from " . \Lobby::l("/admin/lobby-store.php", "Lobby Store"));
          }else{
            echo '<div class="apps">';
            foreach($apps as $app){
              $App = new Apps($app);
            ?>
              <div class="app card">
                <div class="app-inner">
                  <div class="lpane">
                    <a href="<?php echo \Lobby::u("/admin/apps.php?app=$app");?>">
                      <img src="<?php echo $App->info["logo"];?>" />
                    </a>
                  </div>
                  <div class="rpane">
                    <a href="<?php echo \Lobby::u("/admin/apps.php?app=$app");?>" class="name"><?php echo $App->info["name"];?></a>
                    <p><a class="chip">Version <?php echo $App->info["version"];?></a></p>
                    <div style="margin-top: 10px;">
                      <?php
                      if($App->hasUpdate())
                        echo "<cl/>" . \Lobby::l("/admin/check-updates.php", "Update", "class='btn orange'");
                      else if($App->enabled)
                        echo \Lobby::l("/admin/apps.php?app=$app&action=disable" . CSRF::getParam(), "Disable", "class='btn'");
                      else
                        echo \Lobby::l("/admin/apps.php?app=$app&action=enable" . CSRF::getParam(), "Enable", "class='btn green'");
                      echo \Lobby::l("/admin/apps.php?app=$app&action=remove" . CSRF::getParam(), "Remove", "class='btn red'");
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php
            }
            echo '</div>';
          }
        }
        ?>
      </div>
    </div>
  </body>
</html>
