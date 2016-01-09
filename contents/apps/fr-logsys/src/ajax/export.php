<?php
$backupFile = \Lobby::$config['db']['dbname'] . "-" .date("Y-m-d-H-i-s") ."-". \H::randStr(10) .'.gz';
$backupFileLoc = L_DIR . "/contents/extra/" . $backupFile;

$command = "mysqldump --opt --host=". $this->dbinfo['db_host'] ." --port=" . $this->dbinfo['db_port'] . " --user=". $this->dbinfo['db_username'] ." --password=". $this->dbinfo['db_password'] ." ". $this->dbinfo['db_name'] ." ". $this->dbinfo['db_table'] ." | gzip -9 -c > {$backupFileLoc}";
system($command);
sleep(5);

if(file_exists($backupFileLoc)){
  echo \Lobby::l("/contents/extra/$backupFile", "Download SQL File", "class='button red' target='_blank'");
}else{
  echo "It didn't work. Try using phpMyAdmin to export Database. or direclty use the terminal command : <blockquote>$command</blockquote>";
}
