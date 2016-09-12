<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Hooks::doAction("admin.head.begin");
    \Response::head("Admin");
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <h1>Admin</h1>
        <?php
        if(\Lobby\Update::isAvailable()){
          echo sss("Updates Available", "Some updates are available for you to update. Yay!<cl/><a class='btn blue' href='". \Lobby::u("/admin/update.php") ."'>See Updates</a>");
        }
        echo \Lobby::l("admin/apps.php", "Apps") . " will help you to disable or remove apps.<cl/>";
        echo \Lobby::l("admin/lobby-store.php", "Lobby Store") . " will help you to find & install new apps.<cl/>";
        echo \Lobby::l("admin/settings.php", "Settings") . " will help you configure your Lobby installation.<cl/>";
        echo \Lobby::l("admin/update.php", "Updates") . " will allow you to update apps & Lobby itself.<cl/>";

        if(\Lobby\Modules::exists("admin")){
          echo \Lobby::l("admin/login?logout=true", "Log Out", "class='btn'");
        }
        ?>
        <h2>About</h2>
        <p>You are using <b>Lobby <?php echo \Lobby::getVersion(true);?></b>.</p>
        <p>Lobby is an Open Source software. It would mean a lot to us if you ask doubts, report bugs or send your feedback. Please do so through Facebook, Twitter or GitHub</p>
        <a target="_blank" class="btn pink" href="https://www.facebook.com/groups/LobbyOS">Facebook</a>
        <a target="_blank" class="btn blue" href="https://twitter.com/LobbyOS">Twitter</a>
        <a target="_blank" class="btn black" href="https://github.com/subins2000/lobby/issues">GitHub</a>
      </div>
    </div>
  </body>
</html>
