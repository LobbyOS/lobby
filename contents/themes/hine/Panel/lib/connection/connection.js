function checkNetConnection(){
  re="";
  r = Math.round(Math.random() * 10000);
  $.get("http://server.lobby.sim/api/dot.gif",{subins:r},function(d){
    // Net exists
  }).error(function(){
    $(".panel.top .right #net").addClass("off").attr("title", "Offline");
  });
}
// The Connection Item on Panel
checkNetConnection();
