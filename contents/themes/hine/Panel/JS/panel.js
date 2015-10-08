$(document).ready(function(){
  $('.panel.top .left, .panel.top .right').superfish({delay:100});
  $(document).tooltip({
    position: {
      my: "left top+200",
      at: "left bottom",
      collision: "flipfit" 
    }
  });
});
