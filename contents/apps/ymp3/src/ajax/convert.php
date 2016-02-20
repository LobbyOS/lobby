<?php
$v_url = \H::input("url", "POST");
if($v_url !== ""){
  $url_parts = parse_url($v_url);
  parse_str($url_parts['query'], $query);

  $callback_function_name = "jQuery". preg_replace("/\D/g", "", (float) '1.11.2' + (float) rand(0.00000000000000000, 0.10000000000000000)) . "_" . time() - 1;
  $convert = \Requests::request("https://d.yt-downloader.org/check.php", array(
    "User-Agent" => "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36",
    "Host" => "d.yt-downloader.org",
    "Referer" => "https://www.youtube2mp3.cc/api/",
    "Connection" => "keep-alive",
    "Pragma" => "no-cache",
    "Cache-Control" => "no-cache"
  ), array(
    "callback" => $callback_function_name,
    "v" => $query['v'],
    "f" => "mp3",
    "_" => time()
  ))->body;
  
  if(preg_match("/hash\"\:/", $convert)){
    $response = preg_replace("/$callback_function_name\((.*?)\)/", "$1", $convert);
    $response = json_decode($response, true);
  }
  
  echo json_encode($response);
}
?>
