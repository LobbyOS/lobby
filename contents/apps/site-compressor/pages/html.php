<div class="contents">
	<h1>HTML Compressor</h1>
	<p>Paste your HTML code in the textbox below and COMPRESS !</p>
	<form action="<?php echo Helpers::URL();?>" method="POST">
		<center>
			<textarea name="code"></textarea></textarea><cl/>
			<button style="font-size: 18px;">Compress</button>
		</center>
	</form>
	<?php
	if( isset($_POST['code']) ){
		include APP_DIR . "/load.php";
		$code 	= $_POST['code'];
		$cmp	= $SC->_compressor("html", $code);
		$cmp	= htmlspecialchars($cmp);
	?>
		<h2>Compressed Code</h2>
		<p>Here is the compressed code. Hurray!</p>
		<textarea><?php echo $cmp;?></textarea>
		<p>and I left out PHP to itself, because it's tooooo complicated.</p>
	<?php
	}
	?>
	<style>
		textarea{height: 200px;width: 600px;}
	</style>
</div>