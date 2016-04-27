/**
 * The Dashboard Object. For Managing the Tiles
 */
lobby.dash = {  
  /**
   * The registered tiles data
   */
  tiles : [],
  
  /**
   * Add blank tiles and then replace empty tiles with registered tiles
   */
  init : function(adjust){
    lobby.dash.addTiles();
    lobby.dash.addTileEvents();
  },
  
  /**
   * Register a tile to be added
   */
  addTile : function(data){
    if(typeof data === "object"){
      lobby.dash.tiles.push(data);
    }
  },
  
  /**
   * Add all the tiles registered
   */
  addTiles : function(){
    rows = Math.floor($(".workspace").height() / 200);
    cols = Math.floor($(".workspace").width() / 200);
    
    possibleTiles= rows * cols;
    
    pages = Math.ceil(lobby.dash.tiles.length / possibleTiles);
    if(pages > 1){
      for(i = 0;i < pages;i++){
        if(i !== 0){
          $(".tiles-wrapper").append("<li class='tiles' data-page='"+ i +"'></li>");
        }
        $("#bx-pager").append("<li class='tab'><a data-slide-index='"+ i +"'></a></li>");
      }
    }
    
    i = 0;
    curPage = 0;
    $.each(lobby.dash.tiles, function(ignore, data){
      if(i == possibleTiles){
        curPage++;
        i = 0;
      }
      var id = data['id'];
      var html = '<div class="tile"><div class="app" id="'+ id +'" data-mode="none" data-initdelay="50"><a href="'+ lobby.url +'/app/'+ id +'"><div class="inner"><div class="image"><img src="'+ data['img'] +'" height="100%" width="100%"/></div><div class="title">'+ data['name'] +'</div></div></a></div></div>';
      $(".tiles[data-page="+ curPage +"]").append(html);
      i++;
    });
    
    $('.workspace .tiles-wrapper').bxSlider({
      slideMargin: 0,
      speed: 200,
      infiniteLoop: false,
      hideControlOnEnd: true,
      prevText: '',
      nextText: '',
      pagerCustom: "#bx-pager"
    });
  },
  
  /**
   * Align the tiles
   */
  addTileEvents : function(){
    setTimeout(function(){
      src = "";
      $( ".workspace .tiles" ).disableSelection();
      
      $.contextMenu({
        selector: ".tiles .tile .app",
        items: {
          open: {name: "Open App", icon: "open", callback: function(key, opt){ window.location = $(this).find("a[href]").attr("href"); }},
          admin: {name: "Go To Admin", icon: "open", callback: function(key, opt){ lobby.redirect("/admin/app/"+ $(this).attr("id")); }},
          disable: {name: "Disable App", icon: "close", callback: function(key, opt){ lobby.redirect("/admin/apps.php?action=disable&app="+ $(this).attr("id") +"&csrf_token="+ lobby.csrf_token); }},
          remove: {name: "Remove App", icon: "trash", callback: function(key, opt){ lobby.redirect("/admin/apps.php?action=remove&app="+ $(this).attr("id") +"&csrf_token="+ lobby.csrf_token); }}
        }
      });
    }, 10);
    
    /**
     * Change Apps Tab on mouse wheel direction
     */
    $(window).bind('mousewheel DOMMouseScroll', function(event){
      nextPage = parseInt($("#bx-pager a.active").data("slide-index"));
      if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
        // scroll up
        nextPage--;
      }else {
        // scroll down
        nextPage++;
      }
      $("#bx-pager a[data-slide-index="+ nextPage +"]").click();
    });
  },
  
  /**
   * Save the position of a tile after being dragged and dropped
   */
  save : function(){
    var e = {};
    $(".workspace .tiles .tile.taken").each(function(i, elem){
      e[$(this).find(".app").attr("id")] = $(this).index();
    });
    lobby.saveOption("dashItems", JSON.stringify(e));
  }
};
