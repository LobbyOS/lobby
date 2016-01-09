$.extend(lobby.app, {
  networks: {},
  friends: {},
    
  init: function(){
    this.login();
  },
  
  login: function(){
    $.each(this.networks, function(i, network){
      lobby.mod.keyring.get("social_networks", network, function(value){
        value = $.parseJSON(value.replace(/\'/g, '"'));
        lobby.app.ajax("login.php", {"username": value.username, "password": value.password, "network": network}, function(r){
          if(r == "1"){
            lobby.app.friends();
          }
        });
      });
    });
  },
  
  friends: function(){
    lobby.app.ajax("friends.php", {"count": 1}, function(r){
      $(".workspace #load_info").html("Loading friends 0/"+ r);
      for(i=0;i < (r/10);i++){
        start = (r/10) * (parseFloat(i) + 1);
        lobby.app.ajax("friends.php", {"get": 1, "start": start}, function(){
          $(".workspace #load_info").html("Loading friends "+ start +"/"+ r);
        });
      }
    });
  }
});
lobby.app.init();
