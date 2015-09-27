<?php
ini_set("display_errors", "on");
if(isset($_GET['videoId']) && isset($_GET['h']) && isset($_GET['r']) && isset($_GET['name'])){
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Keep-Alive: timeout=1200, max=4100');
  $url = "http://www.youtube-mp3.org/get?ab=128&video_id=".$_GET['videoId']."&h=".$_GET['h']."&r=".$_GET['r'];
  $headers = get_headers($url, 1);
  $redirect = $headers['Location'];
}
?>
<html>
  <head>
    <script>
      function load() {
        var postdata = '<form id="dynForm" method="POST" action="<?php echo $redirect;?>">' +'</form>';
        window.frames[0].document.body.innerHTML = postdata;
        window.frames[0].document.getElementById('dynForm').submit();
      }
    </script>
  </head>
  <body onload="load()">
    <iframe src="about:blank" id="noreferer" style="display:none;"></iframe>
  </body>
</html>