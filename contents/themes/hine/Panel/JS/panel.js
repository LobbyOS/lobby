$(document).ready(function(){
  $('.panel.top .left, .panel.top .right').superfish({
    delay: 100,
    onBeforeShow: function(){
      $(this).css("margin-top", "10px");
    }
  });
  $(document).tooltip({
    position: {
      my: "left top+5",
      at: "left bottom",
      collision: "flipfit" 
    }
  });
});
