<?include("../includes/load.php");?>
<!DOCTYPE html>
<html><head>
 <?$LC->head("Install");?>
</head><body>
 <div class="content">
  <h1><center>Install Lobby</center></h1>
  <?
  if($db->db){
   ser("Error", "Lobby Is Installed. If you want to reinstall, clear the database and remove <b>config.php</b> file.");
  }elseif(!isset($_GET['step'])){
  ?>
  <p>
   I couldn't find the <b>config.php</b> file which contains the configuration of Lobby. Let's create one !<br/>
   Follow the instructions to install Local on your localhost server.<br/><br/>
   Make sure the <b>Lobby</b> directory's permission is set to Read & Write.
  </p><br/>
  <a href="?step=1" class="button">Proceed To Installation</a>
  <?
  }
  if(isset($_GET['step'])){
   if($_GET['step']==1){
    if(isset($_POST['submit'])){
     $dbhost=filt($_POST['host']);
     $port=filt($_POST['port']);
     $dbnm=filt($_POST['db']);
     $user=filt($_POST['dbuser']);
     $pass=filt($_POST['pass']);
     $pref=filt($_POST['prefix']);
     try {
      $db=new PDO("mysql:dbname=$dbnm;host=$dbhost;port=$port", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
     }
     catch( PDOException $Exception ) {
      ser("Error", "Unable to connect. Make sure that the settings you entered are correct. <a href='install.php?step=1'>Try Again</a>");
     }
     if($pref=="" || preg_match("/^[^a-zA-Z]*$/", $pref)){
      ser("Error","A Prefix should only contain alphabetic characters. <a href='install.php?step=1'>Try Again</a>");
     }
     $config_file="<?
\$LC_config = array(
 'host' => '$dbhost',
 'port' => '$port',
 'user' => '$user',
 'pass' => '$pass',
 'db' => '$dbnm',
 'key' => '".uniqueStr(35)."',
 'prefix' => '$pref'
);
\$LC->debug(false);
?>";
     if(!file_put_contents(L_ROOT."config.php", $config_file)){
      ser("Failed Creating Config File.","Make sure the Permission of Lobby Directory ( ".L_ROOT." ) is set to Read & Write (666).");
     }
     include("makeDatabase.php");
     /* Create Tables */
     if(makeDatabase($pref, $db)){
      sss("Success", "Database Tables and configuration file was successfully created.");
      echo '<br/><br/><a href="'.$LC->host.'" class="button">Continue</a>';
     }else{
      ser("Error", "Unable to create Database Tables. Does the tables exist ? Or Does the user have the permissions to create tables ?");
     }
    }else{
  ?>
    <p>
     Fill up the Database Information fields.
    </p>
    <h2>Database Configuration</h2>
    <form action="install.php?step=1" method="POST">
     <table>
      <tbody>
       <tr><td>Database Host</td><td><input type="text" name="host" value="localhost"></td><td>On Most Systems, It's localhost</td></tr>
       <tr><td>Database Port</td><td><input type="text" name="port" value="3306"></td><td>On Most Systems, It's 3306</td></tr>
       <tr><td>Database Name</td><td><input type="text" name="db"></td><td>The name of the database you want to run Lobby in.</td></tr>
       <tr><td>User Name</td><td><input type="text" name="dbuser"></td><td>Your MySQL Username</td></tr>
       <tr><td>Password</td><td><input type="text" name="pass"></td><td>Your MySQL Password</td></tr>
       <tr><td>Table Prefix</td><td><input type="text" name="prefix" value="l_"></td><td>If you want to run multiple Lobby installations in a single database, change this.</td></tr>
       <tr><td><input type="submit" name="submit" value="Create Configuration File"></td></tr>
      </tbody>
     </table>
    </form>
    <style>td{padding-right:10px;padding-bottom:10px;}</style>
  <?
    }
   }
   if($_GET['step']==2){
    
   }
  }
  ?>
 </div>
</body></html>
