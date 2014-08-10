<?php
class Upgrade extends L {
	
	/* Get the Zip File from Server & return the downloaded file location */
	public static function zipFile($url, $zipFile){
		if( !extension_loaded('zip') ){
  			ser("PHP Zip Extension", "I can't find the Zip PHP Extension. Please Install It & Try again");
 		}
 		$GLOBALS['LC']->log("Started Downloading Zip File from {$url} to {$zipFile}");
 		
		$userAgent = 'LobbyBot/0.1 (' . L_SERVER . ')';
		// Get The Zip From Server
 		$ch 			 = curl_init();
 		$zipResource = fopen($zipFile, "w");
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
 		$GLOBALS['LC']->log("Downloaded Zip File from {$url} to {$zipFile}");
 		
 		return $zipFile;
	}
	
	public static function software(){
 		$url = L_SERVER . "/download/lobby/" . getOption("lobby_latest_version");
 		/* Check again if Lobby Directory is writable */
 		if( !is_writable(L_ROOT) ){
  			ser("Error", "<b>" . L_ROOT . "</b> is not writable. Make It Writable & <a href='upgrade.php'>Try again</a>");
 		} 	
 			
 		$zipFile	  = L_ROOT."/contents/upgrade/" . getOption("lobby_latest_version") . ".zip";
 		self::zipFile($url, $zipFile);
 		
 		// Make the Zip Object
 		$zip = new ZipArchive;
 		if( $zip->open($zipFile) != "true" ){
  			ser("Error", "Unable to open Zip File.  <a href='upgrade.php'>Try again</a>");
 		}
 		
 		$GLOBALS['LC']->log("Upgrading Lobby Software From {$zipFile}");
 		/* Extract New Version */
 		$zip->extractTo(L_ROOT);
 		$zip->close();
 		unlink($zipFile);
 		chmod(L_ROOT, 0666);
 		$GLOBALS['LC']->log("Upgraded Lobby Software");
 
 		/* Remove Depreciated Files */
 		if( file_exists(L_ROOT . "/contents/upgrade/removeFiles.php") ){
  			$files = include(L_ROOT . "/contents/upgrade/removeFiles.php");
  			if(count($files)!=0){
   			foreach($files as $file){ // iterate files
    				$file = L_ROOT.$file;
    				$type=filetype($file);
    				if(file_exists($file)){
     					if($type=="file"){
      					unlink($file);
     					}else if($type=="dir"){
      					rmdir($file);
     					}
    				}
   			}
   			unlink(L_ROOT . "/contents/upgrade/removeFiles.php");
   			$GLOBALS['LC']->log("Removed Depreciated Files");
  			}
 		}
 
 		/* Database */
 		if(file_exists(L_ROOT . "/upgrade/sqlExecute.sql")){
  			$GLOBALS['LC']->log("Upgrading Lobby Database");
  			$sqlCode = file_get_contents(L_ROOT . "/upgrade/sqlExecute.sql");
  			$sql = $GLOBALS['db']->prepare($sqlCode);
  			if( !$sql->execute() ){
   			ser("Error", "Database Upgrade Couldn't be made. <a href='upgrade.php'>Try again</a>");
  			}else{
   			unlink(L_ROOT . "/upgrade/sqlExecute.sql");
  			}
  			$GLOBALS['LC']->log("Upgraded Lobby Database");
 		}
 		
 		$oldVer = getOption("lobby_version");
 		saveOption( "lobby_version", getOption("lobby_latest_version") );
 		saveOption( "lobby_version_release", getOption("lobby_latest_version_release") );
 		$GLOBALS['LC']->log("Lobby is successfully Upgraded.");
 		
 		header("Location: " . L_HOST . "/admin/about.php?upgraded=1&oldver={$oldVer}");
	}
	
	/* Upgrade Apps */
	public static function app($id){
 		if($id == ""){
  			ser("Error", "No App Mentioned to upgrade.");
 		}
 		$GLOBALS['LC']->log("Installing Latest Version of App {$id}");
 		
 		$url = L_SERVER . "/download/app/{$id}/latest";
 		if(!is_writable(APPS_DIR)){
  			ser("Error", "<b>".APPS_DIR."</b> is not writable. Make It Writable & Try again");
 		}
 		$userAgent = 'LobbyBot/0.1 (http://lobby.subinsb.com)';
 		$zipFile	  = L_ROOT."/contents/upgrade/{$id}.zip";
 		
 		self::zipFile($url, $zipFile);
 
 		// Un Zip the file
 		$zip = new ZipArchive;
 		if( $zip->open($zipFile) != "true" ){
  			ser("Error", "Unable to open Downloaded App File.");
 		} 
 		/* Extract App */
 		$zip->extractTo(APPS_DIR);
 		$zip->close();
 		unlink($zipFile);
 		$GLOBALS['LC']->log("Installed App {$id}");
 		
 		return true;
	}
}
?>