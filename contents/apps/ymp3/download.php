<?php
ini_set("display_errors", "on");
if(isset($_GET['videoId']) && isset($_GET['h']) && isset($_GET['r']) && isset($_GET['name'])){
 header("Content-Description: File Transfer");
 header("Content-Type: application/octet-stream");
 header('Content-Disposition: attachment; filename="'.urldecode($_GET['name']).'.mp3"');
 header("Content-Transfer-Encoding: binary");
 header('Expires: 0');
 header('Cache-Control: must-revalidate');
 header('Pragma: public');
 header('Keep-Alive: timeout=1200, max=4100');
 $url="http://www.youtube-mp3.org/get?ab=128&video_id=".$_GET['videoId']."&h=".$_GET['h']."&r=".$_GET['r'];
 $local = tempnam(sys_get_temp_dir(), 'Tux');
 $cmd = 'wget -d --header="Host: www.youtube-mp3.org" -O "'.$local.'" "'.$url.'"; echo "subin"';
 $exec = exec($cmd);
 if($exec=="subin"){
  $fp = fopen($local, 'rb');
  $size = filesize($local);
  header("Content-length: $size");
  fpassthru($fp);
 }
}
?>