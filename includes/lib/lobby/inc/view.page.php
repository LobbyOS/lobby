<!DOCTYPE html>
<html>
  <head>
    <?php
    if(\Lobby::status("lobby.admin")){
      \Hooks::doAction("admin.head.begin");
    }else{
      \Hooks::doAction("head.begin");
    }
    \Response::head();
    if(\Lobby::status("lobby.admin")){
      \Hooks::doAction("admin.head.end");
    }else{
      \Hooks::doAction("head.end");
    }
    ?>
  </head>
  <body>
    <?php
    if(\Lobby::status("lobby.admin")){
      \Hooks::doAction("admin.body.begin");
    }else{
      \Hooks::doAction("body.begin");
    }
    ?>
    <div id="workspace">
      <?php
      echo \Response::getPageContent();
      ?>
    </div>
  </body>
</html>
