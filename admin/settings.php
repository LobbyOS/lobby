<html>
  <head>
    <?php
    \Hooks::doAction("admin.head.begin");
    \Response::head("Change Settings");
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <?php
        if(isset($_GET['updated']) && CSRF::check()){
          echo sss("Updated", "Lobby was successfully updated to Version <b>". \Lobby::$version ."</b> from the old ". htmlspecialchars($_GET['oldver']) ." version.");
        }
        if(isset($_POST['update_settings']) && \CSRF::check()){
          $time_zone = Request::postParam("timezone");
          $name = Request::postParam("profile-name");

          if($name !== null){
            Lobby\DB::saveOption("profile-name", $name);
          }

          /**
           * Sadly, PHP supports GMT+ and not UTC+
           */
          if($time_zone === ""){
            Lobby\DB::saveOption("lobby_timezone", "UTC");
            \Lobby\Time::loadConfig();
          }else if(@date_default_timezone_set($time_zone)){
            Lobby\DB::saveOption("lobby_timezone", $time_zone);
            \Lobby\Time::loadConfig();
          }else{
            echo ser("Invalid Timezone", "Your PHP server doesn't support the timezone ".htmlspecialchars($time_zone));
          }
        }
        ?>
        <h1>Settings</h1>
        <form action="<?php echo \Lobby::u();?>" method="POST">
          <input type="hidden" name="update_settings" value="" />
          <?php echo CSRF::getInput();?>
          <label>
            <span>Name</span>
            <input type="text" name="profile-name" placeholder="Your name" value="<?php echo Lobby\DB::getOption("profile-name");?>" />
          </label>
          <label>
            <span title="Time now : <?php echo \Lobby\Time::now();?>">Timezone</span>
            <select id="timezone_string" name="timezone">
              <optgroup label="System">
                <option selected="selected" value="">System Default</option>
              </optgroup>
              <?php
              $regions = array(
                "Africa" => DateTimeZone::AFRICA,
                "America" => DateTimeZone::AMERICA,
                "Antartica" => DateTimeZone::ANTARCTICA,
                "Asia" => DateTimeZone::ASIA,
                "Atlantic" => DateTimeZone::ATLANTIC,
                "Australia" => DateTimeZone::AUSTRALIA,
                "Europe" => DateTimeZone::EUROPE,
                "Indian" => DateTimeZone::INDIAN,
                "Pacific" => DateTimeZone::PACIFIC,
                "UTC" => DateTimeZone::UTC
              );

              $ctz = Lobby\DB::getOption("lobby_timezone");
              foreach($regions as $region => $id){
                $tzs = \DateTimeZone::listIdentifiers($id);
                echo '<optgroup label="'. $region .'">';
                  foreach($tzs as $tz){
                    echo '<option value="'. $tz .'" '. ($ctz === $tz ? "selected" : "") .'>'. $tz .'</option>';
                  }
                echo '</optgroup>';
              }
              ?>
            </select>
          </label>
          <button clear class="btn green">Save Settings</button>
        </form>
        <?php
        /**
         * Check if the current version is not the latest version
         */
        if(\Lobby::$version < \Lobby\DB::getOption("lobby_latest_version")){
        ?>
          <div clear></div>
          <a class="btn red" href="update.php">Update To Version <?php echo Lobby\DB::getOption("lobby_latest_version");?></a>
        <?php
        }
        ?>
      </div>
    </div>
  </body>
</html>
