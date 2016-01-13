lobby.mod.filechooser = {
  inited: false,
  
  init: function(){
    if(!this.inited){
      $(".workspace").append("<div class='Lobby-FS-filechooser'></div>");
    }
  },
  
  dialog: function(path){
    this.init();
    this.
    $(".workspace .Lobby-FS-filechooser").dialog();
  }
};
