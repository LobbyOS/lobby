$.extend(lobby.app, {

  p: ".workspace#pocket ",
  
  init: function(){
    this.loadBalance();
  },

  loadBalance: function(){
    this.ajax("balance.php", {}, function(d){
      $(lobby.app.p + "#balance").text(d);
    });
  }
  
});
lobby.app.init();
