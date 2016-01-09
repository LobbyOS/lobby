<div class="contents">
  <h1>CSS Compressor</h1>
  <p>Paste your CSS code in the textbox below and COMPRESS !</p>
  <form action="<?php echo \Lobby::u();?>" method="POST">
    <center>
      <textarea name="code"></textarea></textarea><cl/>
      <button class="button blue" style="font-size: 18px;">Compress</button>
    </center>
  </form>
  <?php
  if( isset($_POST['code']) ){
    include APP_DIR . "/src/load.php";
    $code = $_POST['code'];
    $cmp = $SC->_compressor("css", $code);
    $cmp = htmlspecialchars($cmp);
  ?>
    <h2>Compressed Code</h2>
    <p>Here is the compressed code. Hurray!</p>
    <textarea><?php echo $cmp;?></textarea>
  <?php
  }
  ?>
  <style>
    textarea{height: 200px;width: 600px;}
  </style>
</div>
