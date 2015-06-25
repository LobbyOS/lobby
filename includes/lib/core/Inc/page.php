<?php
$GLOBALS['AppID'] = $AppID;
?>
<html>
  <head>
    <?php
    \Lobby::doHook("head.begin");
    ?>
    <script>window.lobby = {};<?php if(isset($AppID)){
        echo 'lobby["app"] = { id: "'. $AppID .'", url: "'. APP_URL .'", src: "'. \Lobby::u("/contents/apps/{$AppID}") .'" };';
      }
    ?></script>
    <?php
    \Lobby::head();
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("body.begin");
    ?>
    <div class="workspace" <?php if(isset($AppID)){ echo 'id="'.$AppID.'"'; } ?>>
      <?php
      if(is_array($GLOBALS['workspaceHTML'])){
        $fileLoc = $GLOBALS['workspaceHTML'][0];
        include L_DIR . $fileLoc;
      }else{
        echo $GLOBALS['workspaceHTML'];
      }
      ?>
    </div>
  </body>
</html>
