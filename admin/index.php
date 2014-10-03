<?php include "../load.php";?>
<html>
	<head>
		<?php $LC->head("App Manager");?>
	</head>
	<body>
		<?php include "$docRoot/includes/source/top.php";?>
		<?php include "$docRoot/admin/sidebar.php";?>
		<div class="workspace">
			<div class="contents">
				<h2>Admin</h2>
				<p>Welcome to the Admin panel of Lobby. Here, you can manage your Lobby installation</p>
				<ol>
					<li><?php echo Helpers::link("admin/about.php", "About"); ?></li>
					<li><?php echo Helpers::link("admin/appCenter.php", "App Center"); ?></li>
				</ol>
				<p>Have some problems or want to give a suggestion ? See our <a target="_blank" href="https://github.com/subins2000/lobby/issues">Issues Page</a></p>
			</div>
		</div>
	</body>
</html>