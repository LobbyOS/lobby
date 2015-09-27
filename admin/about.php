<?php include "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Lobby::head("Lobby Info");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    include "$docRoot/admin/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <?php
        if(isset($_GET['updated']) && H::csrf()){
          sss("Updated", "Lobby was successfully updated to Version <b>". getOption("lobby_version") ."</b> from the old ". htmlspecialchars($_GET['oldver']) ." version.");
        }
        ?>
        <h1>About</h1>
        <p>Here is the information about your Lobby install.</p>
        <table border="1" style="margin-top:5px">
          <tbody>
            <tr>
              <td>Version</td>
              <td><?php echo getOption("lobby_version");?></td>
            </tr>
            <tr>
              <td>Release Date</td>
              <td><?php echo getOption("lobby_version_release");?></td>
            </tr>
               <tr>
              <td>Latest Version</td>
              <td><?php echo getOption("lobby_latest_version");?></td>
               </tr>
               <tr>
              <td>Latest Version Release Date</td>
              <td><?php echo getOption("lobby_latest_version_release");?></td>
               </tr>
            </tbody>
          </table>
          <div clear=""></div>
          <a class="button" href="<?php echo L_URL; ?>/admin/update.php">Updates</a>
          <a class='button green' href='<?php echo L_URL;?>/admin/check-updates.php'>Check For Updates</a>
          <?php
          /* Check if the current version is not the latest version */
          if(getOption("lobby_version") != getOption("lobby_latest_version")){
          ?>
            <div clear></div>
            <a class="button" href="update.php">Update To Version <?php echo getOption("lobby_latest_version");?></a>
          <?php
          }
          ?>
         </div>
      </div>
   </body>
</html>
