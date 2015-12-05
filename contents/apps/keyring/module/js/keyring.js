lobby.mod.keyring = {
  get: function(master, key, callback){
    lobby.ajax("get.php", {"master" : master, "key" : key}, function(r){
      r = JSON.parse(r);
      if(r.status == "type_password"){
        $(r.response).dialog({
          modal: true,
          height: 250,
          width: 400,
          buttons: {
            "Unlock KeyRing": function() {
              t = $(this);
              lobby.mod.keyring.KeyGet(master, $(this).find("input[type=password]").val(), key, function(r){
                if(r.status == "password_wrong"){
                  $("<div>Wrong Password</div>").dialog({
                    modal: true,
                    buttons: {
                      Ok: function() {
                        $( this ).dialog("close");
                      }
                    },
                    open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
                  });
                }else if(r.status == "done"){
                  t.dialog("close");
                  callback(r.response);
                }
              });
            },
            Cancel: function() {
              $(this).dialog("close");
            }
          }
        });
      }
    }, "keyring");
  },
  
  add: function(master, key, value, callback){
    if(typeof value == "object"){
      value = JSON.stringify(value);
    }
    lobby.ajax("add.php", {"master" : master, "key" : key, "value" : value}, function(r){
      r = JSON.parse(r);
      status = r.status;
      
      if(status == "master_does_not_exist"){
        callback(status);
      }else if(r.status == "type_password"){
        $(r.response).dialog({
          modal: true,
          height: 250,
          width: 400,
          buttons: {
            "Add Key": function() {
              t = $(this);
              lobby.mod.keyring.KeyAdd(master, $(this).find("input[type=password]").val(), key, value, function(r){
                if(r == "password_wrong"){
                  $("<div>Wrong Password</div>").dialog({
                    modal: true,
                    buttons: {
                      Ok: function() {
                        $( this ).dialog("close");
                      }
                    },
                    open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); }
                  });
                }else{
                  t.dialog("close");
                  callback(r);
                }
              });
            },
            Cancel: function() {
              $(this).dialog("close");
            }
          }
        });
      }
    }, "keyring");
  },
  
  MasterAdd: function(master, name, description, callback){
    callback = typeof callback == "function" ? callback : function(){};
    lobby.ajax("create_master.php", {"master_id": master, "master_name": name, "master_description": description}, function(r){
      r = JSON.parse(r);
      if(r.status == "type_password"){
        $(r.response).dialog({
          modal: true,
          height: 230,
          width: 400,
          buttons: {
            "Create KeyRing": function() {
              t = $(this);
              lobby.ajax("create_master.php", {"master_id": master, "master_name": name, "master_description": description, "master_password": $(this).find("input[type=password]").val()}, function(r){
                t.dialog("close");
                callback();
              }, "keyring");
            },
            Cancel: function() {
              $(this).dialog("close");
            }
          }
        });
      }else if(r.status == "created"){
        callback();
      }
    }, "keyring");
  },
  
  KeyAdd: function(master, password, key, value, callback){
    lobby.ajax("add.php", {"master" : master, "key" : key, "value" : value, "password": password}, function(r){
      r = JSON.parse(r);
      callback(r.status);
    }, "keyring");
  },
  
  KeyGet: function(master, password, key, callback){
    lobby.ajax("get.php", {"master" : master, "key" : key, "password": password}, function(r){
      r = JSON.parse(r);
      callback(r);
    }, "keyring");
  }
};
