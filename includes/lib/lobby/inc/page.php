<?php
$GLOBALS['AppID'] = $AppID;
?>
<html>
  <head>
    <?php
    if(\Lobby::status("lobby.admin")){
      \Lobby::doHook("admin.head.begin");
    }else{
      \Lobby::doHook("head.begin");
    }
    \Lobby::head();
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
    <div class="workspace" <?php if(isset($AppID)){ echo 'id="'.$AppID.'"'; } ?>>
      <?php
      if(is_array($GLOBALS['workspaceHTML'])){
        require_once L_DIR . $GLOBALS['workspaceHTML'][0];
      }else{
        echo $GLOBALS['workspaceHTML'];
      }
      ?>
    </div>
  </body>
</html>
