<div class="panel top">
 	
 	<ul class="left">
 		<?php
 	if(substr(Helpers::curPage(), 0, 6) == "/admin"){
 	?>
		<li class="item prnt menuToggler" style="margin-top: 2px;">
			<img src="<?php echo Helpers::URL("/includes/lib/core/img/menu.png");?>" height="16" width="16" />
		</li>
 	<?php
	}
	?>
 		<?php $LD->panelItems("left"); ?>
 	</ul>
 	<ul class="right">
 		<?php $LD->panelItems("right"); ?>
 	</ul>
</div>