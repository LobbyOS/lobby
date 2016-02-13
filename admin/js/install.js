lobby.load(function(){
  $("#step3 #choose_db_location").live("click", function(){
    default_val = $("#step3 #db_location").val();
    lobby.mod.FilePicker(default_val, function(data){
      clog(data);
    });
  });
});
