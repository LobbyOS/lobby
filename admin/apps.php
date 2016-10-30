<?php
use \Lobby\Apps;
use \Lobby\FS;
use \Lobby\Need;

$appID = Request::get("app");
$action = Request::get("action");
$quick = Request::get("quick") !== null;

/**
* Whether the app info should be shown
*/
$showAppInfo = true;

/**
* Whether this is a request to show a message
*/
$show = Request::get("show") !== null;

if($appID != null){
  $App = new Apps($appID);
  if(!$App->exists)
    Response::showError("Error", "I checked all over, but the app does not exist");
  $appIDEscaped = htmlspecialchars($appID);

  if(!$show && $action !== null && CSRF::check()){
    if($action === "disable"){
      if($App->disableApp())
        Response::redirect("/admin/apps.php?app=$appID&action=disable&show" . CSRF::getParam());
      else
        Response::redirect("/admin/apps.php?app=$appID&action=disable-fail&show" . CSRF::getParam());
    }else if($action === "enable"){
      if($App->enableApp())
        Response::redirect("/admin/apps.php?app=$appID&action=enable&show" . CSRF::getParam());
      else
        Response::redirect("/admin/apps.php?app=$appID&action=enable-fail&show" . CSRF::getParam());
    }
  }
}
?>
<html>
  <head>
    <?php
    \Assets::js("admin.apps.js", "/admin/js/apps.js");
    \Assets::css("apps-grid", "/admin/css/apps-grid.css");
    \Assets::css("apps", "/admin/css/apps.css");

    \Hooks::doAction("admin.head.begin");
    \Response::head("App Manager");
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <?php
        if($appID !== null && !$quick){
        ?>
          <h2><?php echo "<a href='". Lobby::u("/admin/apps.php?app={$App->info['id']}") ."'>". $App->info['name'] ."</a>";?></h2>
          <div id="appNav">
            <p class="chip"><?php echo $App->info['short_description'];?></p>
          </div>
          <?php
          if($action !== null && $show && CSRF::check()){
            switch($action){
              case "disable":
                echo sss("Disabled", "The App <strong>$appIDEscaped</strong> has been disabled.");
                break;
              case "disable-fail":
                echo ser("Error", "The App <strong>$appIDEscaped</strong> couldn't be disabled. Try again.");
                break;
              case "enable":
                echo sss("Enabled", "The App <strong>$appIDEscaped</strong> has been enabled.");
                break;
              case "enable-fail":
                echo ser("Error", "The App couldn't be enabled. Try again.", false);
                break;
            }
          }else if($action !== null && CSRF::check()){
            if($action === "remove"){
              /**
               * Do not show app info during confirmation
               */
              $showAppInfo = false;

              echo sme("Confirm", "<p>Are you sure you want to remove the app <b>$appIDEscaped</b> ? This cannot be undone.</p>" . Lobby::l("/admin/install-app.php?action=remove&app=$appID" . CSRF::getParam(), "Yes, I'm sure", "class='btn red'") . Lobby::l("/admin/apps.php?app=$appID" . CSRF::getParam(), "No, I'm not", "class='btn blue' id='cancel'"));
            }else if($action === "clear-data"){
              $showAppInfo = false;

              echo sme("Confirm", "<p>Are you sure you want to clear the data of app <b>$appIDEscaped</b> ? This cannot be undone.</p>" . Lobby::l("/admin/install-app.php?action=clear-data&app=$appID" . CSRF::getParam(), "Yes, I'm sure", "class='btn red'") . Lobby::l("/admin/apps.php?app=$appID" . CSRF::getParam(), "No, I'm not", "class='btn blue' id='cancel'"));
            }
          }
          if($showAppInfo){
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
                    $requirements = Need::checkRequirements($App->info["require"], false, true);
                    echo "<div class='chip'>Requirements :</div><ul>";
                    foreach($requirements as $dependency => $depInfo){
                      if($depInfo["satisfy"]){
                        echo "<li class='collection-item'>$dependency {$depInfo['require']}</li>";
                      }else{
                        echo "<li class='collection-item red'>$dependency {$depInfo['require']}</li>";
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
                      <td><?php echo $App->dir;?></td>
                    </tr>
                    <tr>
                      <td>Folder</td>
                      <td><h6><?php $folderSize = FS::getSize($App->dir);echo FS::normalizeSize($folderSize);?></h6></td>
                    </tr>
                    <tr>
                      <td title="Size occupied in database">App Data</td>
                      <td>
                        <h6>
                        <?php $dbSize = $App->getDBSize();echo FS::normalizeSize($dbSize);?>
                        <a class="btn red" href="<?php echo \Lobby::u("/admin/apps.php?app=$appID&action=clear-data" . CSRF::getParam());?>">Clear Data</a>
                        </h6>
                      </td>
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
          }
        }else{
        ?>
          <h2>Apps</h2>
          <p>Manage <b>installed apps</b>. You can find and install more Apps from <a href="<?php echo L_URL;?>/admin/lobby-store.php">Lobby Store</a>.</p>
        <?php
          if($action !== null){
            switch($action){
              case "disable":
                echo sss("Disabled", "The App <strong>$appIDEscaped</strong> has been disabled.");
                break;
              case "disable-fail":
                echo ser("Error", "The App <strong>$appIDEscaped</strong> couldn't be disabled. Try again.");
                break;
              case "enable":
                echo sss("Enabled", "The App <strong>$appIDEscaped</strong> has been enabled.");
                break;
              case "enable-fail":
                echo ser("Error", "The App couldn't be enabled. Try again.", false);
                break;
            }
          }

          $apps = Apps::getApps();

          if(empty($apps)){
            echo ser("No Apps", "You haven't installed any apps. <br/>Get great Apps from " . \Lobby::l("/admin/lobby-store.php", "Lobby Store"));
          }else{
            echo '<div class="apps row">';
            foreach($apps as $app){
              $App = new Apps($app);
            ?>
              <div class="app col s12 m4 l3 <?php if($App->hasUpdate()) echo "red"; ?>">
                <div class="app-inner card row">
                  <div class="lpane col s5 m5 l5">
                    <a href="<?php echo \Lobby::u("/admin/apps.php?app=$app");?>">
                      <img src="<?php echo $App->info["logo"];?>" />
                    </a>
                  </div>
                  <div class="rpane col s7 m6 l7">
                    <a href="<?php echo \Lobby::u("/admin/apps.php?app=$app");?>" class="name truncate" title="<?php echo $App->info["name"];?>"><?php echo $App->info["name"];?></a>
                    <div class="actions">
                      <?php
                      echo "<div class='switch col s6 m12 l6'>";
                        if($App->enabled){
                          echo "<a href='". Lobby::u("/admin/apps.php?app=$app&action=disable" . CSRF::getParam()) ."'>";
                            echo "<label>";
                              echo "<input type='checkbox' data-appID='$app' checked='checked' />";
                              echo "<span class='lever' title='Disable app'></span>";
                            echo "</label>";
                          echo "</a>";
                        }else{
                          echo "<a href='". Lobby::u("/admin/apps.php?app=$app&action=enable" . CSRF::getParam()) ."'>";
                            echo "<label>";
                              echo "<input type='checkbox' data-appID='$app' />";
                              echo "<span class='lever' title='Enable app'></span>";
                            echo "</label>";
                          echo "</a>";
                        }
                      echo "</div>";
                      echo "<div class='col s6 m12 l6'>" . Lobby::l("/admin/apps.php?app=$app&action=remove" . CSRF::getParam(), "<i id='delete' class='small' title='Delete app'></i>") . "</div>";
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
