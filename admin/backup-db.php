<?php
require "../load.php";
if(\Lobby::$installed){
  $backupFile = \Lobby::$config['db']['dbname']. "-" .date("Y-m-d H:i:s") . '.gz';
  $backupFileLoc = L_DIR . "/contents/extra/" . $backupFile;
  $command = "mysqldump --opt --host=". \Lobby::$config['db']['host'] ." --port=" . \Lobby::$config['db']['port'] . " --user=". \Lobby::$config['db']['username'] ." --password=". \Lobby::$config['db']['password'] ." ". \Lobby::$config['db']['dbname'] ." | gzip -9 -c > {$backupFileLoc}";
  system($command);
  sleep(5);
  if( file_exists($backupFileLoc) ){
    \Lobby::redirect("/contents/extra/$backupFile");
  }else{
    echo "It didn't work. Try using phpMyAdmin to export Database. or direclty use the terminal command : <blockquote>$command</blockquote>";
  }
}
?>
