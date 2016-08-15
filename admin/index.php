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
        ?>
        <p>Manage your Lobby installation.</p>
        <?php
        echo \Lobby::l("admin/settings.php", "Settings", "class='btn red'") . "&nbsp;";
        echo \Lobby::l("admin/apps.php", "Apps", "class='btn green'") . "&nbsp;";
        echo \Lobby::l("admin/lobby-store.php", "Lobby Store", "class='btn pink'") . "&nbsp;";
        if(\Lobby\Modules::exists("admin")){
          echo \Lobby::l("admin/login?logout=true", "Log Out", "class='btn'");
        }
        ?>
        <h2>About</h2>
        <p>You are using <b>Lobby <?php echo \Lobby::getVersion();?></b>.</p>
        <p>Lobby is an Open Source software. It would mean a lot to us if you ask doubts, report bugs or send your feedback. Please do so through Facebook, Twitter or GitHub</p>
        <a target="_blank" class="btn pink" href="https://www.facebook.com/groups/LobbyOS">Facebook</a>
        <a target="_blank" class="btn blue" href="https://twitter.com/LobbyOS">Twitter</a>
        <a target="_blank" class="btn black" href="https://github.com/subins2000/lobby/issues">GitHub</a>
      </div>
    </div>
  </body>
</html>
