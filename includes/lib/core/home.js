$(document).ready(function(){
	$('.panel.top .left').superfish({delay:100});
	$(document).tooltip({position: { my: "left top+5", at: "left bottom", collision: "flipfit" }});
	$(".menuToggler").live("click", function(){
		if($(".sidebar").is(":visible")){
			$(".sidebar").animate({left: "-210px"}, 1000, function(){
				$(this).hide();
			});
		}else{
			$(".sidebar").show().animate({left: "0px"}, 1000);
		}
	});
});