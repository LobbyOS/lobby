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
});
