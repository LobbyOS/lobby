$(document).ready(function(){
  $('.panel.top .left, .panel.top .right').superfish({delay:100});
  $(document).tooltip({
    position: {
      my: "left top+5",
      at: "left bottom",
      collision: "flipfit" 
    }
  });
  function checkNetConnection(){
    re="";
    r = Math.round(Math.random() * 10000);
    $.get("http://ad.doubleclick.net/dot.gif",{subins:r},function(d){
      // Net exists
    }).error(function(){
      $(".panel.top .right #net").addClass("off").attr("title", "Offline");
    });
  }
  // The Connection Item on Panel
  checkNetConnection();
});
