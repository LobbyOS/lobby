<html>
 <head>
  <?php
  $LC->head();
  ?>
 </head>
 <body>
  	<?php
  	include L_ROOT . "/includes/source/top.php";
  	?>
  	<div class="workspace" <?php $GLOBALS['AppID'] = $AppID;if(isset($AppID)){ echo 'id="'.$AppID.'"'; } ?>>
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