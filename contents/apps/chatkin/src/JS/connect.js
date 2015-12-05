lobby.app.connect = function(n){
  if(n == "facebook"){
    //window.open("http://server.lobby.dev/services/chatkin/login.php?n=facebook", "Login To Facebook", "height=200,width=400");
    
    return true;
  }else{
    return true;
  }
};

$('.workspace #login_form').live('submit', function(event){
  event.preventDefault();
  
  network = $(this).data("network");
  username = $(this).find("input[name=username]").val();
  pass = $(this).find("input[type=password]").val();
  
  $("#login_status").text("Logging In...");
  lobby.app.ajax("login.php", {'network': network, 'username': username, 'password': pass}, function(r){
    if(r == "1"){
      $("#login_status").text("Logged In");
      network_id = network + "_" + (Math.random() + "").substr(2,6);
      lobby.mod.keyring.add("social_networks", network_id, {'username': username, 'password': pass}, function(r){
        if(r == "master_does_not_exist"){
          lobby.mod.keyring.MasterAdd("social_networks", "Social Networks", "Account Information of Your Social Network Sites", function(){
            $('.workspace #login_form').trigger('submit');
          });
        }else if(r == "created"){
          $("#login_status").text("Connected. Finishing...");
          lobby.app.ajax("connected.php", {"network": network, "id": network_id}, function(){
            if(lobby.app.connect(network)){
              lobby.app.redirect("/");
            }
          });
        }
      });
    }else{
      $(".workspace #login_status").text("Password Wrong");
    }
  });
});
