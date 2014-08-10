<?php include("../load.php");?>
<html>
	<head>
		<?php $LC->head("Lobby Info");?>
	</head>
	<body>
		<?php
		include("../includes/source/top.php");
		?>
		<div class="workspace">
			<div class="contents">
				<?php
				if(isset($_GET['upgraded'])){
					sss("Upgraded", "Lobby was successfully upgraded to Version <b>".getOption("lobby_version")."</b> from the old ".$_GET['oldver']." version.");
				}
				?>
				<h2>About</h2>
				<p>You can see the information about your Lobby install.</a></p>
				<table border="1" style="margin-top:5px">
					<tbody>
						<tr>
							<td>Version</td>
							<td><?php echo getOption("lobby_version");?></td>
						</tr>
						<tr>
							<td>Release Date</td>
							<td><?php echo getOption("lobby_version_release");?></td>
						</tr>
     					<tr>
							<td>Latest Version</td>
							<td><?php echo getOption("lobby_latest_version");?></td>
     					</tr>
     					<tr>
							<td>Latest Version Release Date</td>
							<td><?php echo getOption("lobby_latest_version_release");?></td>
     					</tr>
    				</tbody>
    			</table>
    			<div clear></div>
    			<a class="button" href="<?php echo L_HOST; ?>/admin/upgrade.php">See Upgrade Information</a>
    			<a class='button green' href='checkReleases.php'>Check For New Releases</a>
    			<?php
    			/* Check if the current version is not the latest version */
    			if(getOption("lobby_version") != getOption("lobby_latest_version")){
    			?>
    				<div clear></div>
    				<a class="button" href="upgrade.php">Upgrade To Version <?php echo getOption("lobby_latest_version");?></a>
    			<?php
    			}
    			?>
   			</div>
  		</div>
 	</body>
</html>