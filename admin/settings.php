<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Lobby::head("Change Settings");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <?php
        if(isset($_GET['updated']) && H::csrf()){
          sss("Updated", "Lobby was successfully updated to Version <b>". getOption("lobby_version") ."</b> from the old ". htmlspecialchars($_GET['oldver']) ." version.");
        }
        if(isset($_POST['update_settings']) && \H::csrf()){
          /**
           * Sadly, PHP supports GMT+ and not UTC+
           */
          $time_zone = $_POST['timezone'];
          if($time_zone === ""){
            saveOption("lobby_timezone", "UTC");
            \Lobby\Time::loadConfig();
          }else if(@date_default_timezone_set($time_zone)){
            saveOption("lobby_timezone", $time_zone);
            \Lobby\Time::loadConfig();
          }else{
            ser("Invalid Timezone", "Your PHP server doesn't support the timezone ".htmlspecialchars($time_zone));
          }
        }
        ?>
        <h2>Settings</h2>
        <form action="<?php echo \Lobby::u();?>" method="POST">
          <input type="hidden" name="update_settings" value="" />
          <?php echo \H::csrf("i");?>
          <label>
            <span>Timezone</span>
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
              
              $ctz = getOption("lobby_timezone");
              foreach($regions as $region => $id){
                $tzs = \DateTimeZone::listIdentifiers($id);
                echo '<optgroup label="'. $region .'">';
                  foreach($tzs as $tz){
                    echo '<option value="'. $tz .'" '. ($ctz === $tz ? "selected" : "") .'>'. $tz .'</option>';
                  }
                echo '</optgroup>';
              }
              ?>
              <optgroup label="UTC">
                <option value="UTC">UTC</option>
              </optgroup>
              <optgroup label="Manual Offsets">
                <option value="UTC-12">UTC-12</option>
                <option value="UTC-11.5">UTC-11:30</option>
                <option value="UTC-11">UTC-11</option>
                <option value="UTC-10.5">UTC-10:30</option>
                <option value="UTC-10">UTC-10</option>
                <option value="UTC-9.5">UTC-9:30</option>
                <option value="UTC-9">UTC-9</option>
                <option value="UTC-8.5">UTC-8:30</option>
                <option value="UTC-8">UTC-8</option>
                <option value="UTC-7.5">UTC-7:30</option>
                <option value="UTC-7">UTC-7</option>
                <option value="UTC-6.5">UTC-6:30</option>
                <option value="UTC-6">UTC-6</option>
                <option value="UTC-5.5">UTC-5:30</option>
                <option value="UTC-5">UTC-5</option>
                <option value="UTC-4.5">UTC-4:30</option>
                <option value="UTC-4">UTC-4</option>
                <option value="UTC-3.5">UTC-3:30</option>
                <option value="UTC-3">UTC-3</option>
                <option value="UTC-2.5">UTC-2:30</option>
                <option value="UTC-2">UTC-2</option>
                <option value="UTC-1.5">UTC-1:30</option>
                <option value="UTC-1">UTC-1</option>
                <option value="UTC-0.5">UTC-0:30</option>
                <option value="UTC+0">UTC+0</option>
                <option value="UTC+0.5">UTC+0:30</option>
                <option value="UTC+1">UTC+1</option>
                <option value="UTC+1.5">UTC+1:30</option>
                <option value="UTC+2">UTC+2</option>
                <option value="UTC+2.5">UTC+2:30</option>
                <option value="UTC+3">UTC+3</option>
                <option value="UTC+3.5">UTC+3:30</option>
                <option value="UTC+4">UTC+4</option>
                <option value="UTC+4.5">UTC+4:30</option>
                <option value="UTC+5">UTC+5</option>
                <option value="UTC+5.5">UTC+5:30</option>
                <option value="UTC+5.75">UTC+5:45</option>
                <option value="UTC+6">UTC+6</option>
                <option value="UTC+6.5">UTC+6:30</option>
                <option value="UTC+7">UTC+7</option>
                <option value="UTC+7.5">UTC+7:30</option>
                <option value="UTC+8">UTC+8</option>
                <option value="UTC+8.5">UTC+8:30</option>
                <option value="UTC+8.75">UTC+8:45</option>
                <option value="UTC+9">UTC+9</option>
                <option value="UTC+9.5">UTC+9:30</option>
                <option value="UTC+10">UTC+10</option>
                <option value="UTC+10.5">UTC+10:30</option>
                <option value="UTC+11">UTC+11</option>
                <option value="UTC+11.5">UTC+11:30</option>
                <option value="UTC+12">UTC+12</option>
                <option value="UTC+12.75">UTC+12:45</option>
                <option value="UTC+13">UTC+13</option>
                <option value="UTC+13.75">UTC+13:45</option>
                <option value="UTC+14">UTC+14</option>
              </optgroup>
            </select>
            <p>Timestamp Now : <?php echo \Lobby\Time::now();?></p>
          </label>
          <button clear class="button green">Save Settings</button>
        </form>
        <h2>About</h2>
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
            <a class="button red" href="update.php">Update To Version <?php echo getOption("lobby_latest_version");?></a>
          <?php
          }
          ?>
         </div>
      </div>
   </body>
</html>
