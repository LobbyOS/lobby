<?
include "../load.php";
require L_ROOT . "/includes/classes/install.php";
$Install = new Installation;
?>
<!DOCTYPE html>
<html>
	<head>
 		<?$LC->head("Install");?>
	</head>
	<body>
 		<div class="content">
  			<h1><center>Install Lobby</center></h1>
  			<?
  			if($db->db){
   			ser("Error", "Lobby Is Installed. If you want to reinstall, clear the database and remove <b>config.php</b> file.");
  			}elseif(!isset($_GET['step'])){
  			?>
  				<p style="margin: 0px;">
   		 	Lobby hasn't been set up. Let's configure Lobby.<br/>
   		 	Follow the instructions to install Local on your localhost server. <cl/>
   		 	Make sure the <b>Lobby</b> directory's permission is set to Read & Write.
  				</p><cl/>
  				<a href="?step=1" class="button">Proceed To Installation</a>
  			<?
  			}
  			if(isset($_GET['step'])){
   			if($_GET['step'] == 1){
    				$Install->step1();
   			}
   			if($_GET['step'] == 2 ){
    				/* We call it again, so that the user had already went through the First Step */
    				$Install->step1(true);
    				
    				if(isset($_POST['submit'])){
     					$dbhost	 = filt($_POST['host']);
     					$dbport	 = filt($_POST['port']);
     					$dbname   = filt($_POST['db']);
     					$username = filt($_POST['dbuser']);
     					$password = filt($_POST['pass']);
     					$prefix   = filt($_POST['prefix']);
     					
     					/* We give the database config to the Install Class */
     					$Install->dbConfig(array(
     						"host" 	=> $dbhost,
     						"port" 	=> $dbport,
     						"name" 	=> $dbname,
     						"user" 	=> $username,
     						"pass" 	=> $password,
     						"prefix" => $prefix
     					));
     					/* Check if connection to database can be established using the credentials given by the user */
     					$Install->checkDatabaseConnection();
     					
     					if($prefix == "" || preg_match("/^[^a-zA-Z]*$/", $prefix)){
      					ser("Error", "A Prefix should only contain alphabetic characters. <a href='install.php?step=1'>Try Again</a>");
     					}
     					/* Make the Config File */
     					$Install->makeConfigFile();
     			
     					/* Create Tables */
     					if($Install->makeDatabase($prefix)){
		      			sss("Success", "Database Tables and configuration file was successfully created.");
      					echo '<cl/><a href="'.$LC->host.'" class="button">Finish</a>';
		     			}else{
		      			ser("Error", "Unable to create Database Tables. Does the tables exist ? Or Does the user have the permissions to create tables ?");
		     			}
    				}else{
  					?>
    					<p>
     					Fill up the Database Information fields.
    					</p>
    					<h2>Database Configuration</h2>
    					<form action="install.php?step=2" method="POST">
     						<table>
      						<tbody>
       							<tr><td>Database Host</td><td><input type="text" name="host" value="localhost"></td><td>On Most Systems, It's localhost</td></tr>
       							<tr><td>Database Port</td><td><input type="text" name="port" value="3306"></td><td>On Most Systems, It's 3306</td></tr>
       							<tr><td>Database Name</td><td><input type="text" name="db"></td><td>The name of the database you want to run Lobby in.</td></tr>
       							<tr><td>User Name</td><td><input type="text" name="dbuser"></td><td>Your MySQL Username</td></tr>
       							<tr><td>Password</td><td><input type="text" name="pass"></td><td>Your MySQL Password</td></tr>
       							<tr><td>Table Prefix</td><td><input type="text" name="prefix" value="l_"></td><td>If you want to run multiple Lobby installations in a single database, change this.</td></tr>
       							<tr><td><input type="submit" name="submit" value="Install"></td></tr>
       						</tbody>
       					</table>
    					</form>
    					<style>td{padding-right:10px;padding-bottom:10px;}</style>
  					<?
    				}
   			}
  			}
  			?>
 		</div>
	</body>
</html>