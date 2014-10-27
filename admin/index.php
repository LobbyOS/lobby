<?php include "../load.php";?>
<html>
	<head>
		<?php $LC->head("App Manager");?>
	</head>
	<body>
		<?php include "$docRoot/includes/lib/core/php/top.php";?>
		<?php include "$docRoot/admin/sidebar.php";?>
		<div class="workspace">
			<div class="content">
				<h2>Admin</h2>
				<p>Welcome to the Admin panel of Lobby. You can manage your Lobby installation from here</p>
				<ul>
					<li><?php echo Helpers::link("admin/about.php", "About"); ?></li>
					<li><?php echo Helpers::link("admin/appCenter.php", "App Center"); ?></li>
					<li><?php echo Helpers::link("admin/apps.php", "Installed Apps"); ?></li>
				</ul>
				<p>Encoutered a problem or want to make a suggestion ? See our <a target="_blank" href="https://github.com/subins2000/lobby/issues">GitHub Page</a></p>
			</div>
		</div>
	</body>
</html>