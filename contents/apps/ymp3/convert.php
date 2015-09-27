<?php
if(isset($_POST['url'])){
   $url = $_POST['url'];
   if($url==""){
      die("0");
   }else{
      $videoId = \Lobby::loadURL("http://www.youtube-mp3.org/a/pushItem/", array(
       "item" => $url,
       "el" => "na",
       "bf" => "false",
       "r" => time()
      ), "GET");
      if(strlen($videoId) > 20){
      die("0");
      }else{
      $itemInfo = \Lobby::loadURL("http://www.youtube-mp3.org/a/itemInfo/", array(
          "video_id" => $videoId,
          "ac" => "www",
          "t" => "grp",
          "r" => time()
      ), "GET");
      $itemInfo = str_replace("info = ","",$itemInfo);
      $itemInfo = str_replace(";", "", $itemInfo);
      $itemInfo = json_decode($itemInfo, true);
      $newItemInfo = array(
          "id" => $videoId,
          "h" => $itemInfo['h'],
          "title" => $itemInfo['title'],
          "length" => $itemInfo['length'],
          "image" => $itemInfo['image']
      );
      if($newItemInfo['id'] == '$$$ERROR$$$'){
          die(0);
      }
      echo json_encode($newItemInfo);
      }
   }
}else{
 die("0");
}
?>