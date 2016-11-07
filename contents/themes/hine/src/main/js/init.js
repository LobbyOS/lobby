lobby.load(function(){
  $('[title]').tooltip({
    content: function(){
      return $(this).attr('title');
    }
  });

  $("nav .button-collapse").sideNav({
    menuWidth: 200
  });
});
