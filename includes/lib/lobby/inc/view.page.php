<html>
  <head>
    <?php
    if(\Lobby::status("lobby.admin")){
      \Lobby::doHook("admin.head.begin");
    }else{
      \Lobby::doHook("head.begin");
    }
    Response::head();
    if(\Lobby::status("lobby.admin")){
      \Lobby::doHook("admin.head.end");
    }else{
      \Lobby::doHook("head.end");
    }
    ?>
  </head>
  <body>
    <?php
    if(\Lobby::status("lobby.admin")){
      \Lobby::doHook("admin.body.begin");
    }else{
      \Lobby::doHook("body.begin");
    }
    ?>
    <div id="workspace" <?php if(\Lobby\Apps::isAppRunning()){ echo 'id="'. \Lobby\Apps::getInfo("id") .'"'; } ?>>
      <?php
      echo Response::getPageContent();
      ?>
    </div>
  </body>
</html>
