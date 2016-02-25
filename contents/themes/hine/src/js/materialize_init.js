lobby.load(function(){
  $('[title]').each(function(){
    clog($(this).attr("title"));
    $(this).attr("data-tooltip", $(this).attr("title"));
    $(this).tooltip({delay: 50});
    $(this).attr("title", "");
  });
  
  $('select').material_select();
});
