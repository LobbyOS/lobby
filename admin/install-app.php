<!DOCTYPE html>
<html>
  <head>
    <?php
    \Hooks::doAction("admin.head.begin");
    \Assets::js("admin.apps.js", "/admin/js/install-app.js");
    \Response::head("Install App");
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <?php
        $appID = Request::get("app");
        $action = Request::get("action");

        /**
         * Whether this is a request to show a message
         */
        $show = Request::get("show") !== null;

        $displayID = htmlspecialchars($appID);
        $App = new \Lobby\Apps($appID);

        if($appID === null){
          echo ser("Error", "No App is mentioned. Install Apps from <a href='lobby-store.php'>Lobby Store</a>");
        }else if($appID !== null && $action === null && CSRF::check()){
        ?>
          <h1>Install App</h1>
          <p>The install progress will be displayed below. If this doesn't work, try the <?php echo \Lobby::l("/admin/install-app.php?app=$appID&do=alternate-install".CSRF::getParam(), "alternate install");?>.</p>
          <?php
          if(isset($_GET["do"]) && $_GET["do"] === "alternate-install" && CSRF::check()){
          ?>
            <iframe src="<?php echo L_URL . "/admin/download.php?type=app&app={$appID}". CSRF::getParam();?>" style="border: 0;width: 100%;height: 300px;"></iframe>
        <?php
          }else{
        ?>
            <ul id="appInstallationProgress" class="collection"></ul>
            <script>
              lobby.load(function(){
                lobby.installApp("<?php echo $appID;?>", $("#appInstallationProgress"));
              });
            </script>
        <?php
          }
        }else if(!$App->exists){
          echo ser("Error", "App is not installed");
        }else if($action === "enable" && CSRF::check()){
          $App->enableApp();
          echo sss("Enabled", "The App <b>{$displayID}</b> is enabled. The author says thanks. <cl/><a href='".$App->info['url']."' class='btn green'>Open App</a>");
        }else if($action === "remove" && CSRF::check()){
          $App->removeApp();
          echo sss("Removed", "The App <b>{$displayID}</b> was successfully removed.");
        }else if($action === "clear-data" && CSRF::check()){
          if($App->clearData()){
            echo sss("Cleared Data", "The data of <b>{$displayID}</b> was successfully cleared from the database.");
          }
        }
        ?>
      </div>
    <div>
  </body>
</html>
