<?php
use Lobby\Update;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    \Hooks::doAction("admin.head.begin");
    \Response::head("Update");
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <h1>Update</h1>
        <p>Lobby and apps can be updated automatically.</p>
        <a class='btn blue' href='check-updates.php'>Check For Updates</a>
        <a href="<?php echo L_SERVER;?>/docs/update" target="_blank" class="btn pink">Help</a>
        <?php
        $action = Request::postParam("action");
        $step = Request::get("step");

        if($action === null && $step === null){
          if(Update::isCoreAvailable()){
        ?>
            <h2>Lobby</h2>
            <p>
              Welcome To The Lobby Update Page. A latest version is available for you to upgrade.
            </p>
            <blockquote>
              Latest Version is <?php echo Lobby\DB::getOption("lobby_latest_version");?> released on <?php echo date( "jS F Y", strtotime(Lobby\DB::getOption("lobby_latest_version_release")) );?>
            </blockquote>
            <h4>Backup</h4>
            <p style="margin: 10px 0;">
              Lobby will automatically download the latest version and install. In case something happens, Lobby will not be accessible anymore.<cl/>
              So backup your database and Lobby installation before you do anything.
            </p>
            <div clear></div>
            <a class="btn green" href="backup-db.php">Export Lobby Database</a>
            <a class="btn blue" href="backup-dir.php">Export Lobby Folder</a>
            <h4>Release Notes</h4>
            <blockquote>
              <?php echo htmlspecialchars_decode(Lobby\DB::getOption("lobby_latest_version_release_notes"));?>
            </blockquote>
          <?php
            echo '<div style="margin-top: 10px;">';
              echo \Lobby::l("/admin/update.php?step=1" . CSRF::getParam(), "Start Lobby Update", "class='btn btn-large red'");
            echo '</div>';
          }else{
            echo "<h2>Lobby</h2>";
            echo sss("Latest Version", "You are using the latest version of Lobby : <blockquote><b>". Lobby::getVersion(true) . "</b> released on <b>" . Lobby::$versionReleased ."</b></blockquote>There are no new releases yet.");
          }
        }
        if($step !== null && CSRF::check()){
          $step = $step;
          if($step === "1"){
            if(!is_writable(L_DIR)){
              echo ser("Lobby Directory Not Writable", "The Lobby directory (". L_DIR .") is not writable. Make the folder writable to update Lobby.");
            }
          ?>
            <p>
              Looks like everything is ok. Hope you backed up Lobby installation & Database.
              <div clear></div>
              You can update now.
            </p>
          <?php
            echo \Lobby::l("/admin/update.php?step=2" . CSRF::getParam(), "Start Update", "clear class='btn green'");
          }elseif($step == 2){
            $version = Lobby\DB::getOption("lobby_latest_version");
            echo '<iframe src="'. L_URL . "/admin/download.php?type=lobby". CSRF::getParam() .'" style="border: 0;width: 100%;height: 200px;"></iframe>';
          }
        }
        $shouldUpdate = Request::postParam("updateApp");

        if($action === "updateApps" && is_array($shouldUpdate) && CSRF::check()){
          /**
           * Prevent display of Apps' Update List
           */
          $step = 1;

          foreach($shouldUpdate as $appID){
            echo '<iframe src="'. L_URL . "/admin/download.php?type=app&app={$appID}&isUpdate=1". CSRF::getParam() .'" style="border: 0;width: 100%;height: 200px;"></iframe>';
          }
        }
        if($step === null){
          echo "<h2>Apps</h2>";
        }
        $appUpdates = Update::getApps();
        if($step === null && empty($appUpdates)){
          echo "<p>All apps are up to date.</p>";
        }else if($step === null && isset($appUpdates) && count($appUpdates)){
        ?>
          <p>New versions of apps are available. Choose which apps to update from the following :</p>
          <form method="POST" clear>
            <?php echo CSRF::getInput();?>
            <table>
              <thead>
                <tr>
                  <td style='width: 2%;'>Update ?</td>
                  <td style='width: 20%;'>App</td>
                  <td style='width: 5%;'>Current Version</td>
                  <td style='width: 20%;'>Latest Version</td>
                </tr>
              </thead>
              <?php
              echo "<tbody>";
              foreach($appUpdates as $appID => $latest_version){
                $App = new \Lobby\Apps($appID);
                $AppInfo = $App->info;
                echo '<tr>';
                  echo '<td><label><input style="vertical-align:top;display:inline-block;" checked="checked" type="checkbox" name="updateApp[]" value="'. $appID .'" /><span></span></label></td>';
                  echo '<td><span style="vertical-align:middle;display:inline-block;margin-left:5px;">'. $AppInfo['name'] .'</span></td>';
                  echo '<td>'. $AppInfo['version'] .'</td>';
                  echo '<td>'. $latest_version .'</td>';
                echo '</tr>';
              }
              ?>
            </tbody></table>
            <input type="hidden" name="action" value="updateApps" />
            <button class="btn red" clear>Update Selected Apps</button>
          </form>
        <?php
        }
        ?>
      </div>
    </div>
  </body>
</html>
