<div class="sidebar">
	<div style="height:32px;text-align:center;margin-top:10px;">
		<a target="_blank" href="http://lobby.subinsb.com" style="color:white;">Lobby <?php echo getOption("lobby_version");?></a>
	</div>
	<?php
	$links = array(
		"/admin" 				=> "Dashboard",
		"/admin/appCenter.php"	=> "App Center",
		"/admin/apps.php" 		=> "App Manager",
		"/admin/about.php"		=> "About"
	);
	foreach($links as $link => $text){
		if($link == Helpers::curPage()){
			echo Helpers::link($link, $text, "class='link active'");
		}else{
			echo Helpers::link($link, $text, "class='link'");
		}
	}
	?>
</div>