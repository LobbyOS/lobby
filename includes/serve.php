<?php
require_once "../load.php";

$f = $_GET['file'];
$content = "";
$extraContent = "";

if(preg_match("/\.css/", $f)){
  header("Content-type: text/css");
  $css = 1;
}
if(preg_match("/\.js/", $f) && !isset($css)){
  header("Content-type: application/x-javascript");
  $js = 1;
}

if(preg_match("/,/", $f)){
  $files = explode(",", $f);
}else{
  /**
   * Only 1 File is present
   */
  $files = array($f);
}

/* Loop through files and */
foreach($files as $file){
  $file = str_replace(L_URL, "", $file);
  
  if($file == "/includes/lib/jquery/jquery-ui.js" || $file == "/includes/lib/jquery/jquery.js" || $file == "/includes/lib/core/JS/main.js"){
    $extraContent .= \Lobby\FS::get($file);
  }else{
    if(\Lobby\FS::exists($file)){
      $content .= \Lobby\FS::get($file);
    }else{
      $type_of_file = isset($js) ? "JavaScript" : "CSS";
      \Lobby::log("$type_of_file file was not found in location given : $file");
    }
  }
  
  if(isset($css)){
    $to_replace = array(
      "<?L_URL?>" => L_URL
    );
    if(isset($_GET['APP_URL'])){
      $to_replace["<?APP_URL?>"] = urldecode($_GET['APP_URL']);
      $to_replace["<?APP_SRC?>"] = urldecode($_GET['APP_SRC']);
    }
    foreach($to_replace as $from => $to){
      $content = str_replace($from, $to, $content);
    }
  }
}
if(isset($js)){
   $content = "lobby.url='". L_URL ."';" . $content;
   $content = "$(window).load(function(){".$content."});";
}
$merged = $extraContent.$content;

// Add ETag
$etag = hash("md5", $merged);
header("ETag: $etag");

// We make it cachable for the browsers
header("Cache-Control: public");

/**
 * Was it already cached before by the browser ? The old etag will be sent by
 * the browsers as HTTP_IF_NONE_MATCH. We interpret it 
 */
$browserTag = isset($_SERVER["HTTP_IF_NONE_MATCH"]) ? $_SERVER["HTTP_IF_NONE_MATCH"] : 501;

if($browserTag != $etag){
  echo $merged;
}else{
  header("HTTP/1.1 304 Not Modified");
}
?>
