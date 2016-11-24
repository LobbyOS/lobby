lobby.load(function(){
  $("#select_all_apps").live("click", function(){
    if($(this).is(":checked") === false){
      $("#apps_table input[type=checkbox]").prop("checked", false);
    }else{
      $("#apps_table input[type=checkbox]").prop("checked", true);
      $("#combined_actions").show();
    }
  });

  $("#workspace .app .switch input[type=checkbox]").on("change", function(){
    lobby.ar("admin/enable-app", {enable: $(this).is(":checked"), appID: $(this).attr("data-appID")}, function(r){
      if(r === "enable-fail" || r === "disable-fail")
        lobby.redirect("/admin/apps.php?app="+ appID +"&action="+ r +"&quick&show=1" + lobby.csrfToken);
    });
  });
});
