<?php
$GLOBALS['AppID'] = $AppID;
?>
<html>
  <head>
    <?php
    \Lobby::doHook("head.begin");
    ?>
    <script>
      window.tmp = {};window.lobbyExtra = {};<?php if(isset($AppID)){
        echo 'lobbyExtra["app"] = { id: "'. $AppID .'", url: "'. APP_URL .'", src: "'. \Lobby::u("/contents/apps/{$AppID}") .'" };';
      }
    ?></script>
    <?php
    \Lobby::head();
    \Lobby::doHook("head.end");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("body.begin");
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
