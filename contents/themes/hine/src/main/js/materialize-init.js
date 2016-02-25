lobby.load(function(){
  $('[title]').each(function(){
    clog($(this).attr("title"));
    $(this).attr("data-tooltip", $(this).attr("title"));
    $(this).tooltip({delay: 50});
    $(this).attr("title", "");
  });
  
  /**
   * Add <label> after 'select'
   */
  $('select').live("change", function(){
    t = $(this);
    t.material_select("destroy");
    setTimeout(function(){
      t.material_select();
    }, 10);
  });
  $('select').material_select();
});
