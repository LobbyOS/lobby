$.extend(lobby.app, {

  p: ".workspace#pocket ",
  
  init: function(){
    this.loadBalance();
    this.events_binder();
  },
  
  events_binder: function(){
    $("#settings").live("click", function(){
      lobby.app.ajax("settings.html", {}, function(response){
        $("<div id='settings'>"+ response +"</div>").dialog();
      });
    });
  },

  loadBalance: function(){
    this.ajax("balance.php", {}, function(d){
      $(lobby.app.p + "#balance").text(d);
    });
  }
  
});
lobby.app.init();
