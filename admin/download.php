<?php
require_once __DIR__ . "/../load.php";
require_once L_DIR . "/includes/src/Update.php";

header("Content-type: text/html");
header('Cache-Control: no-cache');

$id = H::i("id");
$type = H::i("type");

// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);

//Flush (send) the output buffer and turn off output buffering
//ob_end_flush();
while (@ob_end_flush());
         
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);

if($id == null || H::csrf() == false){
  exit;
}

if($type == "app"){
  $app = \Lobby\Server::Store(array(
    "get" => "app",
    "id" => $id
  ));
          
  if($app == "false"){
    echo "Error - App '<b>{$id}</b>' does not exist in Lobby.";
    exit;
  }
  $name = $app['name'];
}else{
  $name = "Lobby $id";
}
$GLOBALS['name'] = $name;
?>
<p>
  Do NOT close this window.
</p>
<p>
  Downloading <b><?php echo $name;?></b>...
</p>
<p id='downloadStatus'></p>
<?php
flush();

function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array("", "KB", "M", "G", "T")[floor($base)];
  return round(pow(1024, $base - floor($base)), 1) . $suffix;
}

$GLOBALS['last'] = 0;
\Lobby\Update::$progress = function($resource, $download_size, $downloaded, $upload_size, $uploaded = ""){
  /**
   * On new versions of cURL, $resource parameter is not passed
   * So, swap vars if it doesn't exist
   */
  if(!is_resource($resource)){
    $uploaded = $upload_size;
    $upload_size = $downloaded;
    $downloaded = $download_size;
    $download_size = $resource;
  }
  if($download_size > 1000 && $downloaded > 0){
    $percent = round($downloaded / $download_size  * 100, 0);
  }else{
    $percent = 1;
  }
  if($GLOBALS['last'] != $percent || isset($GLOBALS['non_percent'])){
    $GLOBALS['last'] = $percent;
    if($download_size > 0){
      $rd_size = convertToReadableSize($download_size);
      echo "<script>document.getElementById('downloadStatus').innerHTML = 'Downloaded $percent% of {$rd_size}';</script>";
    }else{
      $downloaded = convertToReadableSize($downloaded);
      $GLOBALS['non_percent'] = 1;
      echo "<script>document.getElementById('downloadStatus').innerHTML = 'Downloaded {$downloaded}';</script>";
    }
    flush();
    if($percent == 100 && !isset($GLOBALS['install-msg-printed'])){
      echo "<p>Installing <b>{$GLOBALS['name']}</b>...</p>";
      $GLOBALS['install-msg-printed'] = 1;
      flush();
    }
  }
};

if($type == "app" && \Lobby\Update::app($id)){
  echo "Installed - The app has been installed. <a target='_parent' href='". L_URL ."/admin/install-app.php?action=enable&id={$_GET['id']}". H::csrf("g") ."'>Enable the app</a> to use it.";
}else if($type == "lobby" && $redirect = \Lobby\Update::software()){
  echo "<a target='_parent' href='$redirect'>Updated Lobby</a>";
}
