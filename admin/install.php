<?php
require "../load.php";
require L_DIR . "/includes/src/Install.php";
?>
<!DOCTYPE html>
<html>
  <head>
     <?php
     \Lobby\UI\Themes::loadTheme();
     \Lobby::addStyle("install", "/admin/css/install.css");
     \Lobby::head("Install");
     ?>
  </head>
  <body class="workspace">
     <div class="contents">
        <h1 style="text-align: center;">
          <?php echo \Lobby::l(L_URL, "Install Lobby");?>
        </h1>
        <?php
        if(\Lobby::$installed && H::input("step") != 3){
          sss("<a href='". L_URL ."'>Lobby Installed</a>", "Lobby Is Installed. If you want to reinstall, delete the database tables and remove <b>config.php</b> file.<cl/>If you want to just remake the <b>config.php</b> file, don't delete the database tables, delete the existing <b>config.php</b> file and do ". \Lobby::l("/admin/install.php?step=1", "this installation") ." until Step 3 where \"Database Tables Exist\" error occur");
        }elseif(!isset($_GET['step'])){
        ?>
          <p>Welcome to the Lobby Installation process. Thank you for downloading Lobby.</p>
          <p>For further help, see <a target='_blank' href='http://lobby.subinsb.com/docs/quick'>Quick Install</a>.</p>
          <p>To start Installation, click the Install button</p>
          <center clear>
            <a href="?step=1<?php echo H::csrf("g");?>" class="button red" style="font-size: 18px;width: 200px;">Install</a>
          </center>
        <?php
        }
        if(isset($_GET['step'])){
          if($_GET['step'] == 1 && H::csrf()){
        ?>
            <h2>Requirements</h2>
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
                    sss("Ok", "Your PHP version is compatible with Lobby");
                  }else{
                    $error = 1;
                    ser("Not Ok", "Lobby requires atleast PHP version 5.3");
                  }
                  ?></td>
                </tr>
                <tr>
                  <td>PHP Output Buffering</td>
                  <td><?php if(ini_get('output_buffering') != "Off"){
                    sss("Ok", "Ouput Buffering is enabled");
                  }else{
                    $error = 1;
                    ser("Not Ok", "Lobby needs Output Buffering to be turned on.");
                  }
                  ?></td>
                </tr>
                <tr>
                  <td>PHP PDO Extension</td>
                  <td><?php if (extension_loaded('pdo')){
                    sss("Ok", "PDO extension is enabled");
                  }else{
                    $error = 1;
                    ser("Not Ok", "PDO extension seems to be missing");
                  }
                  ?></td>
                </tr>
                <?php
                if(ini_get('output_buffering') != "Off"){
                ?>
                  <tr>
                    <td>MySQL 5.0</td>
                    <td><?php
                    ob_start(); 
                      phpinfo(INFO_MODULES); 
                    $info = ob_get_contents(); 
                    ob_end_clean();
                    
                    $mysql_version = stristr($info, 'Client API version'); 
                    preg_match('/[1-9].[0-9].[1-9][0-9]/', $mysql_version, $match); 
                    $mysql_version = $match[0];
                    if(version_compare($mysql_version, '5.0') >= 0){
                      sss("Ok", "Your MySQL version is compatible with Lobby");
                    }else{
                      $error = 1;
                      ser("Not Ok", "Lobby requires atleast MySQ version 5.0");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>PHP JSON Extension</td>
                    <td><?php if (extension_loaded('json')){
                      sss("Ok", "JSON extension is enabled");
                    }else{
                      $error = 1;
                      ser("Not Ok", "JSON extension seems to be missing");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>PHP Zip Extension</td>
                    <td><?php if (extension_loaded('zip')){
                      sss("Ok", "Zip extension is enabled");
                    }else{
                      $error = 1;
                      ser("Not Ok", "Zip extension seems to be missing");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>Apache mod_rewrite Module</td>
                    <td><?php if (preg_match("/mod_rewrite/", $info)){
                      sss("Ok", "Apache mod_rewrite module is enabled");
                    }else{
                      $error = 1;
                      ser("Not Ok", "Apache mod_rewrite module is not enabled");
                    }
                    ?></td>
                  </tr>
                  <tr>
                    <td>Permissions</td>
                    <td><?php if (is_writable(L_DIR)){
                      sss("Ok", "Lobby directory is writable.");
                    }else{
                      $error = 1;
                      ser("Not Ok", "Lobby directory is not writable. Please make it writable. Here's the location : <blockquote>". L_DIR ."</blockquote>");
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
              <a href="?step=2<?php echo H::csrf("g");?>" class="button orange" id="step1_continue">Proceed To Installation</a>
          <?php
            }else{
              echo "<p>Cannot Procced to Installation. Please make the requirements satisfied.</p>";
            }
          }elseif($_GET['step'] == 3){
            echo "<h2>Safety</h2>";
            $safe = \Lobby\Install::safe();
            if($safe == "configFile"){
              ser("Permission Error", "The <b>config.php</b> file still has write permission. Change the permission to Read Only.");
            }
            if($safe !== true){
              echo "<a class='button' href='javascript:;' onclick='window.location = window.location;'>Check Again</a>";
            }else{
              \Lobby::redirect("/#");
            }
          }elseif($_GET['step'] == 2 && H::csrf()){
            /**
             * We call it again, so that the user had already went through the First Step
             */
            
            if(\Lobby\Install::step1() === false){
              // The stuff mentioned in step 1 hasn't been done
            }elseif(isset($_POST['submit'])){
              $dbhost = \H::input('dbhost', "POST");
              $dbport = \H::input('dbport', "POST");
              $dbname = \H::input('dbname', "POST");
              $username = \H::input('dbusername', "POST");
              $password = \H::input('dbpassword', "POST");
              $prefix = \H::input('prefix', "POST");

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
                ser("Error", "A Prefix should only contain basic Latin letters, digits 0-9, dollar, underscore and shouldn't exceed 50 characters.<cl/>" . \Lobby::l("/admin/install.php?step=2" . H::csrf("g"), "Try Again", "class='button'"));
              }elseif(\Lobby\Install::checkDatabaseConnection() !== false){
                /**
                 * Make the Config File
                 */
                \Lobby\Install::makeConfigFile();
           
                /**
                 * Create Tables
                 */
                if(\Lobby\Install::makeDatabase($prefix)){
                  sss("Success", "Database Tables and <b>config.php</b> file was successfully created.");
                  /**
                   * Enable app lEdit
                   */
                  \Lobby::$installed = true;
                  \Lobby\DB::init();
                  $App = new \Lobby\Apps("ledit");
                  $App->enableApp();
                  echo '<cl/><a href="?step=3" class="button">Proceed</a>';
                }else{
                  ser("Unable To Create Database Tables", "Are there any tables with the same name ? Or Does the user have the permissions to create tables ?<cl/>The <b>config.php</b> file is created. To try again, remove the <b>config.php</b> file and click the button. <cl/>" . \Lobby::l("/admin/install.php?step=2" . H::csrf("g"), "Try Again", "class='button'"));
                }
              }
            }else{
            ?>
              <h2>Database</h2>
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
                        <input type="text" name="dbhost" value="localhost">
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
                      <td>The name of the database you want to run Lobby in. <b>Database should exist</b> and Lobby won't create DB if it doesn't exist.</td>
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
                        <button name="submit" style="width:200px;font-size:15px;" class="button green">Install Lobby</button>
                      </td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
                <?php H::csrf(1);?>
              </form>
            <?php
            }
          }
        }
        ?>
     </div>
  </body>
</html>
