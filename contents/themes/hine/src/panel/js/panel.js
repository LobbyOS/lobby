lobby.load(function(){
  /**
   * Panel
   */
  $('nav .left, nav .right').superfish({
    delay: 100
  });
  
  $("#notifyToggle").live("click", function(){
    $("#notifyBox").toggle("show");
  });
  
  lobby.notify.push = function(info){
    pushItem = $("<div class='notifyItem row' id='notifyItem"+ info["id"] +"'><div class='col m2'><span></span></div><div class='col m10'>"+ info["contents"] +"</div></div>");
    if(lobby.notify.box.find("#notifyItem" + info["id"]).length === 0){
      pushItem.prependTo(lobby.notify.box);
    }else{
      lobby.notify.box.find("#notifyItem" + info["id"]).replaceWith(pushItem);
    }
  };
});
