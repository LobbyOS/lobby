$(document).ready(function(){
 $('.panel.top .left').superfish({delay:100});
 setInterval(function(){
  $(document).tooltip({position: { my: "left top+5", at: "left bottom", collision: "flipfit" }});
 },2000);
});
