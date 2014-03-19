<?
function doUpgrade(){
 $url="http://lobby.host/downloads/lobby_".str_replace(".", "-", getOption("lobby_latest_version"));
 if(!is_writable(L_ROOT)){
  ser("Error", "<b>".L_ROOT."</b> is not writable. Make It Writable & <a href='upgrade.php'>Try again</a>");
 }
 if(!extension_loaded('zip')){
  ser("PHP Zip Extension", "I can't find the Zip PHP Extension. Please Install It & <a href='upgrade.php'>Try again</a>");
 }
 $userAgent = 'LobbyBot/0.1 (http://lobby.subinsb.com)';
 $zipFile=L_ROOT."contents/upgrade/".getOption("lobby_latest_version").".zip";
 // Get The Zip From Server
 $ch = curl_init();
 $zipResource=fopen($zipFile, "w");
 curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_FAILONERROR, true);
 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_AUTOREFERER, true);
 curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
 curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
 curl_setopt($ch, CURLOPT_FILE, $zipResource);
 $page = curl_exec($ch);
 if(!$page) {
  ser("Error", curl_error($ch));
 }
 curl_close($ch);
 // Un Zip the file
 $zip = new ZipArchive;
 if($zip->open($zipFile) != "true"){
  ser("Error", "Unable to open Zip File.  <a href='upgrade.php'>Try again</a>");
 }
 
 /* Extract New Version */
 $zip->extractTo(L_ROOT);
 $zip->close();
 unlink($zipFile);
 chmod(L_ROOT, 0666);
 
 /* Remove Depreciated Files */
 if(file_exists(L_ROOT."contents/upgrade/removeFiles.php")){
  $files = include(L_ROOT."contents/upgrade/removeFiles.php");
  if(count($files)!=0){
   foreach($files as $file){ // iterate files
    $file=L_ROOT.$file;
    $type=filetype($file);
    if(file_exists($file)){
     if($type=="file"){
      unlink($file);
     }else if($type=="dir"){
      rmdir($file);
     }
    }
   }
   unlink(L_ROOT."contents/upgrade/removeFiles.php");
  }
 }
 
 /* Database */
 if(file_exists(L_ROOT."upgrade/sqlExecute.sql")){
  $sqlCode=file_get_contents(L_ROOT."upgrade/sqlExecute.sql");
  $sql=$db->prepare($sqlCode);
  if(!$sql->execute()){
   ser("Error", "Database Upgrade Couldn't be made. <a href='upgrade.php'>Try again</a>");
  }else{
   unlink(L_ROOT."upgrade/sqlExecute.sql");
  }
 }
 $oldVer=getOption("lobby_version");
 saveOption("lobby_version", getOption("lobby_latest_version"));
 saveOption("lobby_version_release", getOption("lobby_latest_version_release"));
 header("Location: ".L_HOST."/admin/about.php?upgraded=1&oldver=".$oldVer);
}
function appUpgrade($id){
 if($id==""){
  ser("Error", "No App Mentioned to upgrade.");
 }
 $url="http://lobby.host/downloads/app_".$id;
 if(!is_writable(APP_DIR)){
  ser("Error", "<b>".APP_DIR."</b> is not writable. Make It Writable & Try again");
 }
 if(!extension_loaded('zip')){
  ser("PHP Zip Extension", "I can't find the Zip PHP Extension. Please Install It & Try again");
 }
 $userAgent = 'LobbyBot/0.1 (http://lobby.subinsb.com)';
 $zipFile=L_ROOT."contents/upgrade/".$id.".zip";
 // Get The Zip From Server
 $ch = curl_init();
 $zipResource=fopen($zipFile, "w");
 curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_FAILONERROR, true);
 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_AUTOREFERER, true);
 curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
 curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
 curl_setopt($ch, CURLOPT_FILE, $zipResource);
 $page = curl_exec($ch);
 if(!$page) {
  ser("Error", curl_error($ch));
 }
 curl_close($ch);
 // Un Zip the file
 $zip = new ZipArchive;
 if($zip->open($zipFile) != "true"){
  ser("Error", "Unable to open Downloaded Plugin File.");
 } 
 /* Extract App */
 $zip->extractTo(APP_DIR);
 $zip->close();
 unlink($zipFile);
 return true;
}
?>
