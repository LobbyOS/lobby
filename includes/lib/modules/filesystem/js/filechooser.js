lobby.mod.filechooser = {
  inited: false,
  
  init: function(){
    if(!this.inited){
      $(".workspace").append("<div class='Lobby-FS-filechooser'></div>");
    }
  },

  dialog: function(path, cb){
    cb = typeof cb !== "function" ? function(){} : cb;
    this.init();
    $(".workspace .Lobby-FS-filechooser").dialog();
  }
};
lobby.mod.FileChooser = function(path, callback){
  return lobby.mod.filechooser.dialog(path, callback);
};

lobby.load(function(){
  $("[data-lobby]").live("click", function(){
    if($(this).data("lobby") === "filechooser" && $(this).data("lobby-input") !== undefined){
      fc = lobby.mod.FileChooser($(this).data("lobby-input"), function(path){
        alert(path);
      });
    }
  });
});
