/**
 * The Dashboard Object. For Managing the Tiles
 */
lobby.dash = {
  /**
   * The position of tiles data.
   * The positions that are saved by the user of each tiles
   */
  data : {},
  
  /**
   * The registered tiles data
   */
  tiles : {
    "app" : []
  },
  
  opt  : {},
  
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
  addTile : function(type, data){
    lobby.dash.tiles[type].push(data);
  },
  
  /**
   * Add all the tiles registered
   */
  addTiles : function(){
    $.each(lobby.dash.tiles, function(type, items){
      if(type == "app" && typeof items == "object" && items.length != 0){
        $.each(items, function(i, data){
          var id = data['id'];
          var html = '<div class="tile"><div class="app" id="'+ id +'" data-mode="none" data-initdelay="50"><a href="'+ lobby.url +'/app/'+ id +'"><div class="inner"><div class="image"><img src="'+ data['img'] +'" height="100%" width="100%"/></div><div class="title">'+ data['name'] +'</div></div></a></div></div>';
          if(typeof lobby.dash.data[id] != "undefined"){
            $(".tiles").append(html);
          }
        });
      }
    });
  },
  /**
   * Add the Draggable and Droppable events to all tiles
   */
  addTileEvents : function(){
    setTimeout(function(){
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
