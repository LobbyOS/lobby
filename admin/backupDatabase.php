<?php
require "../load.php";
if(file_exists("../config.php") && L_DB_CONFIG_SET){
 $backupFile = L_DB_CONFIG_DB . "-" .date("Y-m-d-H-i-s") . '.gz';
 $backupFileLoc = L_ROOT . "/admin/" . $backupFile;
 $command = "mysqldump --opt --host=".L_DB_CONFIG_HOST." --port=" . L_DB_CONFIG_PORT . " --user=".L_DB_CONFIG_USER." --password=".L_DB_CONFIG_PASS." ".L_DB_CONFIG_DB." | gzip -9 -c > $backupFile";
 system($command);
 sleep(2);
 if( file_exists($backupFileLoc) ){
 	header("Location: ".L_HOST."/admin/$backupFile");
 }else{
 	echo "It didn't work. Try using phpMyAdmin to export Database. or direclty use the terminal command : <blockquote>$command</blockquote>";
 }
}
?>