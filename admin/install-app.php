<?php
require "../load.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php 
    \Lobby::doHook("admin.head.begin");
    \Assets::js("admin.apps.js", "/admin/js/install-app.js");
    \Lobby::head("Install App");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <?php
        $id = H::i("id");
        $displayID = htmlspecialchars($id);
        
        if($id == null){
          ser("Error", "No App is mentioned. Install Apps from <a href='lobby-store.php'>Lobby Store</a>");
        }
        if(H::i("action") == "enable" && H::csrf()){
          $App = new \Lobby\Apps($id);
          if(!$App->exists){
            ser("Error", "App is not installed");
          }
          $App->enableApp();
          sss("Enabled", "The App <b>{$displayID}</b> is enabled. The author says thanks. <cl/><a href='".$App->info['URL']."' class='btn green'>Open App</a>");
        }
        if(H::i("action") == "remove" && H::csrf()){
          $App = new \Lobby\Apps($id);
          if(!$App->exists){
            ser("Error", "App is not installed");
          }
          $App->removeApp();
          sss("Removed", "The App <b>{$displayID}</b> was successfully removed.");
        }
        if($id != null && H::i("action") == null && H::csrf()){
        ?>
          <h1>Install App</h1>
          <p>The install progress will be displayed below. If this doesn't work, try the <?php echo \Lobby::l("/admin/install-app.php?id=$id&do=alternate-install".csrf("g"), "alternative install");?>.</p>
          <?php
          if(isset($_GET["do"]) && $_GET["do"] === "alternate-install" && csrf()){
          ?>
            <iframe src="<?php echo L_URL . "/admin/download.php?type=app&id={$id}". H::csrf("g");?>" style="border: 0;width: 100%;height: 300px;"></iframe>
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
