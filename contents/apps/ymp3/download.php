<?
if(isset($_GET['videoId']) && isset($_GET['h']) && isset($_GET['r']) && isset($_GET['name'])){
 $url="http://www.youtube-mp3.org/get?ab=128&video_id=".$_GET['videoId']."&h=".$_GET['h']."&r=".$_GET['r'];
 header("Content-type: audio/mp3");
 header('Content-Disposition: attachment; filename="'.urldecode($_GET['name']).'.mp3"');
 $downloadedFileName = time().".mp3";
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 $downloadedFile = fopen($downloadedFileName, 'w+');
 curl_setopt($ch, CURLOPT_FILE, $downloadedFile);
 curl_exec ($ch);
 curl_close ($ch);
 fclose($downloadedFile);
 readfile($downloadedFileName);
 unlink($downloadedFileName);
}
?>
