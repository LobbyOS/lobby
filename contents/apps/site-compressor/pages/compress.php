<?php include "../load.php";?>
<!DOCTYPE html>
<html>
	<head>
		<title>Site Compressor from subinsb.com</title>
		<style>*{font-family:Ubuntu;}.status{font-size:13px;margin:2px;}</style>
	</head>
	<body>
		<?php
		if(isset($_POST['siteDetails'])){
			$_POST['options'] = isset($_POST['options']) ? $_POST['options']:array();
			$starttime = microtime(true);
			$SC->makeOptions($_POST['siteDetails'], $_POST['options']);
			$SC->checkOptions();
			$SC->startCompress();
			$endtime = microtime(true);
			$duration = round($endtime - $starttime, 4);
			$SC->status("Site Compression Finished In $duration seconds");
		}else{
			$SC->ser("Not enough data");
		}
		?>
	</body>
</html>