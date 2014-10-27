rowItems = {0 : []};
// Make tiles
var w = $(".workspace").width();
var h = $(".workspace").height();

var xItems = Math.round($(".tiles").width() / 200);
var yItems = Math.round($(".tiles").height() / 200);
var items = xItems * yItems;
for(i = 0;i < items;i++){
	$(".tiles").append("<div class='tile'></div>");
}
$(".tiles").width(xItems * 200);
// Dynamic scrolling
var offset = (parseFloat($(".workspace").height()) + $(".panel").height()) - (parseFloat($(".tile:last").css("top")) + 200);
$(".tiles").css({
	"bottom" 	: offset,
	"top"		: offset
});

localStorage['a'] = JSON.stringify( $(".apps #anagram").data("LiveTile") );
$(".workspace").niceScroll({
	horizrailenabled : true
});

// Add contents to tiles
lobby.dash = {
	data : {},
	addTile : function(type, data){
		if(type == "app" && typeof data == "object"){
			var id	 = data['id'];
			var html = '<div class="app" id="'+ id +'" data-mode="none" data-initdelay="50"><a href="'+ lobby.host +'/app/'+ id +'"><div class="inner"><div class="image"><img src="'+ data['img'] +'" height="100%" width="100%"/></div><div class="title" style="color:black;">'+ data['name'] +'</div></div></a></div>';
			if(typeof lobby.dash.data[id] != "undefined"){
				$(".tiles .tile").eq(lobby.dash.data[id]).html(html).addClass("taken ui-draggable");
			}else{
				$(".tiles .tile").not(".taken").first().html(html).addClass("taken ui-draggable");
			}
		}
		lobby.dash.addTileEvents();
	},
	addTileEvents : function(){
		setTimeout(function(){
			//$(".tiles .tile").liveTile();
			$(".workspace .tiles .tile").each(function(){
				if(typeof $(this).data("ui-draggable") != "undefined" && typeof $(".workspace .tiles .tile").data("ui-droppable") != "undefined"){
					$(this).draggable("destroy");
					$(this).droppable("destroy");
				}
			});
			src = "";
			$(".workspace .tiles .tile").draggable({
				cursor: 'move',
				revert: "invalid",
				start: function(event, ui) {
					if(!$(this).hasClass("taken")){
						return false;
					}
					src = $(this);
				},
				stop: function(){
					lobby.dash.addTileEvents();
				}
			});
			$(".workspace .tiles .tile").droppable({
				accept : ".workspace .tiles .tile.taken",
				hoverClass: "tile-placeholder",
				drop: function (event, ui) {
					k = ui.draggable.attr("style", "");
					if($(this).hasClass("taken")){
						p = $(this).clone();
						src.replaceWith(p);
					}else{
						src.after("<div class='tile'></div>");
					}
					$(this).replaceWith(k);
					lobby.dash.addTileEvents();
					lobby.dash.save();
				},
				zIndex: 2
			});
			$( ".workspace .tiles" ).disableSelection();
		}, 10);
	},
	save : function(){
		var e = {};
		$(".workspace .tiles .tile.taken").each(function(i, elem){
			e[$(this).find(".app").attr("id")] = $(this).index();
		});
		lobby.saveOption("dashItems", JSON.stringify(e));
	}
};
lobby.dash.addTileEvents();