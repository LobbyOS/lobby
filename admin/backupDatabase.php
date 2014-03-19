<?
require("../includes/load.php");
if(file_exists("../config.php") && L_DB_CONFIG_SET){
 $backupFile = L_DB_CONFIG_DB . "-" .date("Y-m-d-H-i-s") . '.gz';
 $command = "mysqldump --opt -h ".L_DB_CONFIG_HOST." -u ".L_DB_CONFIG_USER." -p".L_DB_CONFIG_PASS." ".L_DB_CONFIG_DB." | gzip -9 -c > $backupFile";
 system($command);
 header("Location: ".L_HOST."/admin/$backupFile");
}
?>
