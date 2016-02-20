<?php
ini_set("display_errors", "on");
if(isset($_GET['hash']) && $_GET['s']){
  $servers = array (
    1 => 'gpkio',
    2 => 'hpbnj',
    3 => 'macsn',
    4 => 'pikku',
    5 => 'fgkzc',
    6 => 'hmqbu',
    7 => 'kyhxj',
    8 => 'nwwxj',
    9 => 'sbist',
    10 => 'ditrj',
    11 => 'qypbr',
    12 => 'wiyqr',
    13 => 'xxvcy',
    14 => 'afyzk',
    15 => 'kjzmv',
    16 => 'txrys',
    17 => 'kzrzi',
    18 => 'rmira',
    19 => 'umbbo',
    20 => 'aigkk',
    21 => 'qgxhg',
    22 => 'twrri',
    23 => 'fkaph',
  );
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Keep-Alive: timeout=1200, max=4100');
  $url = "http://". $servers[$_GET['s']] .".yt-downloader.org/download.php?id=" . $_GET['hash'];
  $redirect = $url;
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
