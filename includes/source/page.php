<?php
$GLOBALS['AppID'] = $AppID;
?>
<html>
	<head>
		<script>window.lobbyExtra = {};<?php if(isset($AppID)){
				echo 'lobbyExtra.appSource = "'.APP_SOURCE.'";';
				echo 'lobbyExtra.appPage = "'.APP_URL.'";';
			}
		?></script>
		<?php
		$LC->head();
		?>
	</head>
	<body>
		<?php
		include L_ROOT . "/includes/source/top.php";
		?>
		<div class="workspace" <?php if(isset($AppID)){ echo 'id="'.$AppID.'"'; } ?>>
			<?php
			if(is_array($GLOBALS['workspaceHTML'])){
				include $GLOBALS['workspaceHTML'][0];
			}else{
				echo $GLOBALS['workspaceHTML'];
			}
			?>
		</div>
	</body>
</html>