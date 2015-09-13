<?php include "../load.php";?>
<html>
  <head>
    <?php \Lobby::head("Admin");?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    include "$docRoot/admin/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <h1>Admin</h1>
        <p>Welcome to the Admin panel of Lobby. You can manage your Lobby installation from here</p>
        <ul>
          <li><?php echo \Lobby::l("admin/about.php", "About"); ?></li>
          <li><?php echo \Lobby::l("admin/apps.php", "Installed Apps"); ?></li>
          <li><?php echo \Lobby::l("admin/lobby-store.php", "Lobby Store"); ?></li>
          <?php
          if(\Lobby\Modules::exists("admin")){
          ?>
            <li><?php echo \Lobby::l("admin/login?logout=true", "Log Out"); ?></li>
          <?php
          }
          ?>
        </ul>
        <p>Encoutered a problem or want to make a suggestion ? See our <a target="_blank" href="https://github.com/subins2000/lobby/issues">GitHub Repo</a></p>
      </div>
    </div>
  </body>
</html>
