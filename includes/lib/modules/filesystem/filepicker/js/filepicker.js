lobby.mod.filepicker = {
  
  u: "",
  inited: false,
  
  init: function(){
    if(!this.inited){
      this.u = lobby.url + "/includes/lib/modules/filesystem/filepicker";
      $(".workspace").append("<div class='Lobby-FS-filepicker'><iframe src='"+ this.u + "/dialog.php" +"'><"+"/iframe><"+"/div>");
    }
  },

  dialog: function(path, cb){
    cb = typeof cb !== "function" ? function(){} : cb;
    this.init();
    $(".workspace .Lobby-FS-filepicker iframe").attr("src", this.u + "/dialog.php");
    $(".workspace .Lobby-FS-filepicker").dialog({
      width: "auto",
      height: "auto",
    });
  }
};
lobby.mod.FilePicker = function(path, callback){
  return lobby.mod.filepicker.dialog(path, callback);
};

lobby.load(function(){
  $("[data-lobby]").live("click", function(){
    if($(this).data("lobby") === "filepicker" && $(this).data("lobby-input") !== undefined){
      fc = lobby.mod.FilePicker($(this).data("lobby-input"), function(path){
        alert(path);
      });
    }
  });
});
