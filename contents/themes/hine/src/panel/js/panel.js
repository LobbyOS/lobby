lobby.load(function(){
  /**
   * Panel
   */
  $('nav .panel-left, nav .panel-right').superfish({
    delay: 100
  });

  $("#notifyToggle").live("click", function(){
    $("#notifyBox").toggle("show");
  });

  $(document).mouseup(function (e){
    var container = $("#notifyBox");
    if (!container.is(e.target) && container.parent().has(e.target).length === 0){
      container.hide();
    }
  });

  lobby.notify.push = function(info){
    if(typeof info["removed"] !== "undefined"){
      lobby.notify.box.find("#notifyItem" + info["id"]).remove();
    }else{
      iconURL = info["iconURL"] === null ? lobby.url +"/admin/image/clear.gif" : info["iconURL"];

      pushItem = $("<a href='"+ info["href"] +"' class='notifyItem row' id='notifyItem"+ info["id"] +"'><div class='col s1 m2'><img class='notifyItemIcon"+ info["icon"] +"' src='"+ iconURL +"' /></div><div class='col s11 m10'>"+ info["contents"] +"</div></a>");

      if(lobby.notify.box.find("#notifyItem" + info["id"]).length === 0){
        pushItem.prependTo(lobby.notify.box);
      }else{
        lobby.notify.box.find("#notifyItem" + info["id"]).replaceWith(pushItem);
      }
    }
  };

  lobby.notify.onNewItems = function(){
    $("#notifyToggle").addClass("active");
  };
});
