<?php require "../load.php";?>
<!DOCTYPE html>
<html>
  <head>
    <?php 
    \Lobby::doHook("admin.head.begin");
    \Lobby::addScript("admin.apps.js", "/admin/js/apps.js");
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
        if(H::i("id") == null){
          ser("Error", "No App is mentioned. Install Apps from <a href='lobby-store.php'>Lobby Store</a>");
        }
        if(H::i("action") == "enable" && H::csrf()){
          $App = new \Lobby\Apps($_GET['id']);
          if(!$App->exists){
            ser("Error", "App is not installed");
          }
          $App->enableApp();
          sss("Enabled", "The App <b>{$_GET['id']}</b> is enabled. The author says thanks. <cl/><a href='".$App->info['URL']."' class='btn green'>Open App</a>");
        }
        if(H::i("action") == "remove" && H::csrf()){
          $App = new \Lobby\Apps($_GET['id']);
          if(!$App->exists){
            ser("Error", "App is not installed");
          }
          $App->removeApp();
          sss("Removed", "The App <b>{$_GET['id']}</b> was successfully removed.");
        }
        $id = H::i("id");
        if($id != null && H::i("action") == null && H::csrf()){
        ?>
          <h1>Install App</h1>
          <iframe src="<?php echo L_URL . "/admin/download.php?type=app&id={$id}". H::csrf("g");?>" style="border: 0;width: 100%;height: 200px;"></iframe>
        <?php
        }
        ?>
      </div>
    <div>
  </body>
</html>
