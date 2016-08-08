<?php
require "../load.php";

$install_step = Request::get('step');
?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    \Lobby\UI\Themes::loadTheme();
    \Hooks::doAction("head.begin");
   
    /**
     * Install Head
     */
    \Assets::css("install", "/admin/css/install.css");
    \Assets::js("install", "/admin/js/install.js");
    \Response::head("Install");
   
    \Hooks::doAction("head.end");
    ?>
  </head>
  <body id="workspace">
     <div class="contents" id="<?php
      $steps = array(
        "1", "2", "3", "4"
      );
      if(in_array($install_step, $steps)){
        echo "step$install_step";
      }
     ?>">
        <h1 style="text-align: center;">
          <?php echo \Lobby::l(L_URL, "Install Lobby");?>
        </h1>
        <?php
        if(\Lobby::$installed && Request::get("step") !== "4"){
          echo sss("<a href='". L_URL ."'>Lobby Installed</a>", "Lobby Is Installed. If you want to reinstall, delete the database tables and remove <b>config.php</b> file.<cl/>If you want to just remake the <b>config.php</b> file, don't delete the database tables, delete the existing <b>config.php</b> file and do ". \Lobby::l("/admin/install.php?step=1", "this installation") ." until Step 3 where \"Database Tables Exist\" error occur");
        }else if($install_step === null){
        ?>
          <p>Welcome to the Lobby Installation process. Thank you for downloading Lobby.</p>
          <p>For further help, see <a target='_blank' href='http://lobby.subinsb.com/docs/quick'>Quick Install</a>.</p>
          <p>To start Installation, click the Install button</p>
          <center clear>
            <a href="?step=1<?php echo CSRF::getParam();?>" class="btn red" style="font-size: 18px;width: 200px;">Install</a>
          </center>
        <?php
        }
        if(isset($install_step)){
          if($install_step === "1" && CSRF::check()){
            if(\Lobby\Install::step1()){
        ?>
              <h3>Requirements</h3>
              <p>Your system must meet the requirements to install Lobby.</p>
              <table>
                <tbody>
                  <tr>
                    <td>Requires</td>
                    <td>Status</td>
                  </tr>
                  <tr>
                    <td>PHP 5.3</td>
                    <td><?php if(version_compare(PHP_VERSION, '5.3') >= 0){
                      echo sss("Ok", "Your PHP version is compatible with Lobby");
                    }else{
                      $error = 1;
                      echo ser("Not Ok", "Lobby requires atleast PHP version 5.3");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>PHP Output Buffering</td>
                    <td><?php if(ini_get('output_buffering') != "Off"){
                      echo sss("Ok", "Ouput Buffering is enabled");
                    }else{
                      $error = 1;
                      echo ser("Not Ok", "Lobby needs Output Buffering to be turned on.");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>PHP PDO Extension</td>
                    <td><?php if (extension_loaded('pdo')){
                      echo sss("Ok", "PDO extension is enabled");
                    }else{
                      $error = 1;
                      echo ser("Not Ok", "PDO extension seems to be missing");
                    }
                    ?></td>
                  </tr>
                  <?php
                  if(ini_get('output_buffering') != "Off"){
                    ob_start(); 
                      phpinfo(INFO_MODULES); 
                    $info = ob_get_contents(); 
                    ob_end_clean();
                  ?>
                    <tr>
                      <td>PHP JSON Extension</td>
                      <td><?php if (extension_loaded('json')){
                        echo sss("Ok", "JSON extension is enabled");
                      }else{
                        $error = 1;
                        echo ser("Not Ok", "JSON extension seems to be missing");
                      }
                      ?></td>
                    </tr>
                    <tr>
                      <td>PHP Zip Extension</td>
                      <td><?php if (extension_loaded('zip')){
                        echo sss("Ok", "Zip extension is enabled");
                      }else{
                        $error = 1;
                        echo ser("Not Ok", "Zip extension seems to be missing");
                      }
                      ?></td>
                    </tr>
                    <?php
                    ob_start(); 
                      phpinfo(INFO_GENERAL); 
                    $g_info = ob_get_contents(); 
                    ob_end_clean();
                    $server_software = stristr($g_info, 'Server API'); 
                    if(preg_match("/\>Apache/", $server_software)){
                    ?>
                      <tr>
                        <td>Apache mod_rewrite Module</td>
                        <td><?php if (preg_match("/mod_rewrite/", $info)){
                          echo sss("Ok", "Apache mod_rewrite module is enabled");
                        }else{
                          $error = 1;
                          echo ser("Not Ok", "Apache mod_rewrite module is not enabled");
                        }
                        ?></td>
                      </tr>
                    <?php
                    }
                    ?>
                    <tr>
                      <td>Permissions</td>
                      <td><?php if (is_writable(L_DIR)){
                        echo sss("Ok", "Lobby directory is writable.");
                      }else{
                        $error = 1;
                        echo ser("Not Ok", "Lobby directory is not writable. Please make it writable. Here's the location : <blockquote>". L_DIR ."</blockquote>");
                      }
                      ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
              <?php
              if(!isset($error)){
              ?>
                <a href="?step=2<?php echo CSRF::getParam();?>" class="btn orange" id="continue">Proceed To Installation</a>
            <?php
              }else{
                echo "<p>Cannot Procced to Installation. Please make the requirements satisfied.</p>";
              }
            }
          }else if($install_step === "4"){
            echo "<h2>Safety</h2>";
            $safe = \Lobby\Install::safe();
            if($safe === "configFile"){
              echo ser("Permission Error", "The <b>config.php</b> file still has write permission. Change the permission to Read Only.");
            }
            if($safe !== true){
              echo "<a class='btn' href='javascript:;' onclick='window.location = window.location;'>Check Again</a>";
            }else{
              \Response::redirect("/#");
            }
          }else if($install_step === "2" && CSRF::check()){
            ob_start(); 
              phpinfo(INFO_MODULES); 
            $info = ob_get_contents(); 
            ob_end_clean();
          ?>
            <h3>Choose Database System</h3>
            <table>
              <tbody>
                <tr>
                  <td width="50%"><?php
                    $mysql_version = stristr($info, 'Client API version');
                    preg_match('/[1-9].[0-9].[1-9][0-9]/', $mysql_version, $match); 
                    $mysql_version = $match[0];
                    if(version_compare($mysql_version, '5.0') >= 0){
                      echo "<a class='btn green' href='?step=3&db_type=mysql". CSRF::getParam() ."'>MySQL</a>";
                    }else{
                      echo "<a class='btn disabled'>MySQL Not Available</a><p>Lobby Requires MySQL version atleast 5.0</p>";
                    }
                  ?></td>
                  <td width="50%"><?php
                    $sqlite_version = stristr($info, 'SQLite Library'); 
                    preg_match('/[1-9].[0-9].[1-9][0-9]/', $sqlite_version, $match); 
                    $sqlite_version = isset($match[0]) ? $match[0] : "";
                    if(version_compare($sqlite_version, '3.8.0') >= 0){
                      $whitelist = array(
                          '127.0.0.1',
                          '::1'
                      );
                      if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                        /**
                         * Localhost
                         */
                        echo "<a class='btn green' href='?step=3&db_type=sqlite". CSRF::getParam() ."'>SQLite</a>";
                      }else{
                        /**
                         * Give warning when using SQLite on a web server
                         */
                        echo "<a class='btn red' href='?step=3&db_type=sqlite". CSRF::getParam() ."'>SQLite</a><p style='color:red;'>WARNING<br/>It is very unsafe to use SQLite on a non localhost server</p>";
                      }
                    }else{
                      echo "<a class='btn disabled'>SQLite Not Available</a><p>Lobby Requires SQLite version atleast 3.8</p>";
                    }
                  ?></td>
                </tr>
              </tbody>
            </table>
          <?php
          }else if($install_step === "3" && CSRF::check()){
            $db_type = Request::get("db_type");
            /**
             * We call it again, so that the user had already went through the First Step
             */
            if(\Lobby\Install::step1() === false){
              // The stuff mentioned in step 1 hasn't been done
            }else if(isset($_POST['submit'])){
              if($db_type === "mysql"){
                $dbhost = \Request::postParam('dbhost', "");
                $dbport = \Request::postParam('dbport', "");
                $dbname = \Request::postParam('dbname', "");
                $username = \Request::postParam('dbusername', "");
                $password = \Request::postParam('dbpassword', "");
                $prefix = \Request::postParam('prefix', "");
                
                if($dbhost === "" || $dbport === "" || $dbname === "" || $username === ""){
                  echo ser("Empty Fields", "Buddy, you left out some details.<cl/>" . \Lobby::l("/admin/install.php?step=3&db_type=mysql" . CSRF::getParam(), "Try Again", "class='btn orange'"));
                  }else{
                  /**
                   * We give the database config to the Install Class
                   */
                  \Lobby\Install::dbConfig(array(
                    "host" => $dbhost,
                    "port" => $dbport,
                    "dbname" => $dbname,
                    "username" => $username,
                    "password" => $password,
                    "prefix" => $prefix
                  ));
                  
                  /**
                   * First, check if prefix is valid
                   * Check if connection to database can be established using the credentials given by the user
                   */
                  if($prefix == "" || preg_match("/[^0-9,a-z,A-Z,\$,_]+/i", $prefix) != 0 || strlen($prefix) > 50){
                    echo ser("Error", "The Prefix should only contain alphabets, digits (0-9), dollar or underscore and shouldn't exceed 50 characters.<cl/>" . \Lobby::l("/admin/install.php?step=3&db_type=mysql" . CSRF::getParam(), "Try Again", "class='btn orange'"));
                  }else if(\Lobby\Install::checkDatabaseConnection() !== false){
                    /**
                     * Create Tables
                     */
                    if(\Lobby\Install::makeDatabase($prefix)){
                      /**
                       * Make the Config File
                       */
                      \Lobby\Install::makeConfigFile();
                    
                      \Lobby::$installed = true;
                      \Lobby\DB::__constructStatic();
                      
                      /**
                       * Enable app lEdit
                       */
                      $App = new \Lobby\Apps("ledit");
                      $App->enableApp();
                      
                      echo sss("Success", "Database Tables and <b>config.php</b> file was successfully created.");
                      echo '<cl/><a href="?step=4'. CSRF::getParam() .'" class="btn">Proceed</a>';
                    }else{
                      echo ser("Unable To Create Database Tables", "Are there any tables with the same name ? Or Does the user have the permissions to create tables ? Error :<blockquote>". \Lobby\Install::$error ."</blockquote>" . \Lobby::l("/admin/install.php?step=2" . CSRF::getParam(), "Try Again", "class='btn'"));
                    }
                  }
                }
              }else{
                $db_loc = $_POST['db_location'];
                $db_create = \Lobby\Install::createSQLiteDB($db_loc);
                
                /**
                 * Prefix is "l_" and can't be changed
                 */
                if($db_create && \Lobby\Install::makeDatabase("l_", "sqlite")){
                  /**
                   * We give the database config to the Install Class
                   */
                  \Lobby\Install::dbConfig(array(
                    /**
                     * Make path relative if DB file in Lobby dir
                     */
                    "path" => str_replace(L_DIR, "", $db_loc),
                    "prefix" => "l_"
                  ));
                
                  /**
                   * Make the Config File
                   */
                  \Lobby\Install::makeConfigFile("sqlite");
                  
                  /**
                   * Enable app lEdit
                   */
                  \Lobby::$installed = true;
                  \Lobby\DB::__constructStatic();

                  $App = new \Lobby\Apps("ledit");
                  $App->enableApp();
                  
                  echo sss("Success", "Database and <b>config.php</b> file was successfully created.");
                  echo '<cl/><a href="?step=4'. CSRF::getParam() .'" class="btn">Proceed</a>';
                }else{
                  echo ser("Couldn't Make SQLite Database", "I was unable to make the database. Error :<blockquote>". \Lobby\Install::$error ."</blockquote> <cl/>" . \Lobby::l("/admin/install.php?step=3&db_type=sqlite" . CSRF::getParam(), "Try Again", "class='btn'"));
                }
              }
            }else{
              if($db_type === "mysql"){
            ?>
                <h3>Database</h3>
                <p>Provide the database credentials. Double check before submitting</p>
                <form action="<?php \Lobby::u();?>" method="POST">
                  <table>
                    <thead>
                      <tr>
                        <td width="20%">Name</td>
                        <td width="40%">Value</td>
                        <td width="40%">Description</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Database Host</td>
                        <td>
                          <input type="text" name="dbhost" value="127.0.0.1">
                        </td>
                        <td>The hostname of database</td>
                      </tr>
                      <tr>
                        <td>Database Port</td>
                        <td>
                          <input type="text" name="dbport" value="3306">
                        </td>
                        <td>On Most Systems, It's 3306</td>
                      </tr>
                      <tr>
                        <td>Database Name</td>
                        <td>
                          <input type="text" name="dbname" />
                        </td>
                        <td>The name of the database you want to run Lobby in. Lobby will create DB if it doesn't exist.</td>
                      </tr>
                      <tr>
                        <td>User Name</td>
                        <td>
                          <input type="text" name="dbusername" />
                        </td>
                        <td>Your MySQL Username</td>
                      </tr>
                      <tr>
                        <td>Password</td>
                        <td>
                          <input type="password" name="dbpassword" />
                        </td>
                        <td>Your MySQL Password</td>
                      </tr>
                      <tr>
                        <td>Table Prefix</td>
                        <td>
                          <input type="text" name="prefix" value="l_" />
                        </td>
                        <td>The name of tables created by Lobby would start with this value</td>
                      </tr>
                      <tr>
                        <td></td>
                        <td>
                          <button name="submit" style="width:200px;font-size:15px;" class="btn green">Install Lobby</button>
                        </td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                  <?php echo CSRF::getInput();?>
                </form>
            <?php
              }else if($db_type === "sqlite"){
              ?>
                <h3>Database</h3>
                <form action="<?php \Lobby::u();?>" method="POST">
                  <p>The location of the SQLite database file :</p>
                  <label>
                    <input type="text" name="db_location" id="db_location" value="<?php echo \Lobby\FS::loc("/contents/extra/lobby_db.sqlite");?>" />
                  </label>
                  
                  <button name="submit" style="width:200px;font-size:15px;" class="btn green">Install Lobby</button>
                  <input type="hidden" name="db_type" value="sqlite" />
                  <?php echo CSRF::getInput();?>
                </form>
              <?php
              }else{
                echo ser("Error", "Uh... You didn't mention the DBMS to use");
              }
            }
          }
        }
        if($install_step === "3"){
        ?>
          <script>
          $(document).ready(function(){
            clog("ccc");
            function getTimeZone() {
                var offset = new Date().getTimezoneOffset(), o = Math.abs(offset);
                return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
            }
            lobby.ajax("admin/ajax/set-timezone.php", {offset: getTimeZone()});
          });
          </script>
        <?php
        }
        ?>
     </div>
  </body>
</html>
