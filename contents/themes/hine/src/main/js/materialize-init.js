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
  $('select').each(function(){
    t=$(this);
    if(!t.parents().hasClass("ui-datepicker")){
      t.material_select();
      t.live("change", function(){
        t.material_select("destroy");
        setTimeout(function(){
          t.material_select();
        }, 1000);
      });
    }
  });
});
