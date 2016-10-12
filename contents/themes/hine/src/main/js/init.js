lobby.load(function(){
  $('[title]').tooltip({
    content: function(){
      return $(this).attr('title');
    }
  });

  $("#workspace ul:not([class])").addClass("collection").find("li").addClass("collection-item");

  $("nav .button-collapse").sideNav({
    menuWidth: 200
  });
});
