<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Response::head("Admin");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div id="workspace">
      <div class="content">
        <h1>Admin</h1>
        <p>Here, you can manage your Lobby installation :</p>
        <?php
        echo \Lobby::l("admin/settings.php", "Settings", "class='btn red'") . "&nbsp;";
        echo \Lobby::l("admin/apps.php", "Installed Apps", "class='btn green'") . "&nbsp;";
        echo \Lobby::l("admin/lobby-store.php", "Lobby Store", "class='btn blue'") . "&nbsp;";
        if(\Lobby\Modules::exists("admin")){
          echo \Lobby::l("admin/login?logout=true", "Log Out", "class='btn'");
        }
        ?>
        <h2>Thank You</h2>
        <p>We, the <a href="https://github.com/orgs/LobbyOS/people" target="_blank">Lobby Team</a> would like to thank you for installing Lobby.</p>
        <p>Lobby is in <b>public beta mode</b>, so bugs may exist and some things may not work for you.</p>
        <p>As you know, Lobby is an <a href="https://en.wikipedia.org/wiki/Open-source_software" target="_blank">Open Source Software</a>. We will be very glad and happy if you report <b>any kinds of problems/bugs</b> you faced :</p>
        <p>Encoutered a problem or want to make a suggestion ? Please do it on our <a target="_blank" class="btn orange" href="https://github.com/subins2000/lobby/issues">GitHub Repo</a></p>
      </div>
    </div>
  </body>
</html>
