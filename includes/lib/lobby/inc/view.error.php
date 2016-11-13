<!DOCTYPE html>
<html>
  <head>
    <?php
    /**
     * Load CSS Only
     */
    echo Assets::getServeLinkTag();
    ?>
    <title><?php echo $title;?> - Lobby</title>
  </head>
  <body>
    <div id="workspace">
      <div class="contents">
        <h1><?php echo $title;?></h1>
        <p><?php echo $description;?></p>
        <p>
          <a href="#" onclick="window.history.go(-1)">Return to previous page</a>
        </p>
        <p>
          &copy; <a target='_blank' href='//lobby.subinsb.com'>Lobby</a> 2014 - <?php echo date("Y");?>
        </p>
      </div>
    </div>
  </body>
</html>
