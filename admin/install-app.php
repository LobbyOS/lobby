<?php
require "../load.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php 
    \Lobby::doHook("admin.head.begin");
    \Assets::js("admin.apps.js", "/admin/js/install-app.js");
    \Response::head("Install App");
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
        $id = Request::get("id");
        $displayID = htmlspecialchars($id);
        
        if($id == null){
          echo ser("Error", "No App is mentioned. Install Apps from <a href='lobby-store.php'>Lobby Store</a>");
        }
        if(Request::get("action") == "enable" && CSRF::check()){
          $App = new \Lobby\Apps($id);
          if(!$App->exists){
            echo ser("Error", "App is not installed");
          }
          $App->enableApp();
          echo sss("Enabled", "The App <b>{$displayID}</b> is enabled. The author says thanks. <cl/><a href='".$App->info['url']."' class='btn green'>Open App</a>");
        }
        if(Request::get("action") == "remove" && CSRF::check()){
          $App = new \Lobby\Apps($id);
          if(!$App->exists){
            echo ser("Error", "App is not installed");
          }
          $App->removeApp();
          echo sss("Removed", "The App <b>{$displayID}</b> was successfully removed.");
        }
        if($id != null && Request::get("action") == null && CSRF::check()){
        ?>
          <h1>Install App</h1>
          <p>The install progress will be displayed below. If this doesn't work, try the <?php echo \Lobby::l("/admin/install-app.php?id=$id&do=alternate-install".CSRF::getParam(), "alternate install");?>.</p>
          <?php
          if(isset($_GET["do"]) && $_GET["do"] === "alternate-install" && CSRF::check()){
          ?>
            <iframe src="<?php echo L_URL . "/admin/download.php?type=app&id={$id}". CSRF::getParam();?>" style="border: 0;width: 100%;height: 300px;"></iframe>
        <?php
          }else{
        ?>
            <ul id="appInstallationProgress" class="collection"></ul>
            <script>
              lobby.load(function(){
                lobby.installApp("<?php echo $id;?>", $("#appInstallationProgress"));
              });
            </script>
        <?php
          }
        }
        ?>
      </div>
    <div>
  </body>
</html>
