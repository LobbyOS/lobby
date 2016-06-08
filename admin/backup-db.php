<?php
require "../load.php";
if(\Lobby::$installed){
  $backupFile = \Lobby::getConfig('db', 'dbname'). "-" .date("Y-m-d H:i:s") . '.gz';
  $backupFileLoc = L_DIR . "/contents/extra/" . $backupFile;
  $command = "mysqldump --opt --host=". \Lobby::getConfig('db', 'host') ." --port=" . \Lobby::getConfig('db', 'port') . " --user=". \Lobby::getConfig('db', 'username') ." --password=". \Lobby::getConfig('db', 'password') ." ". \Lobby::getConfig('db', 'dbname') ." | gzip -9 -c > {$backupFileLoc}";
  system($command);
  sleep(5);
  if( file_exists($backupFileLoc) ){
    Response::redirect("/contents/extra/$backupFile");
  }else{
    echo "It didn't work. Try using phpMyAdmin to export Database. or direclty use the terminal command : <blockquote>$command</blockquote>";
  }
}
?>
