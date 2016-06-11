$(document).ready(function(){
    tinymce.init({
      selector: "#workspace #editor",
      plugins: "autoresize"
    });
   /**
    * The App Parent Element URI
    */
   var app = "#workspace";
   
   $(app + " #save.btn").live("click", function(){
      if(tinyMCE.activeEditor.getContent() === ""){
        $(app + " #error").text("Please Enter Something :-(").fadeIn().delay(2000).fadeOut();
      }else{
        key = $(app + " #saveName").val(); // The key Name
        value = tinyMCE.activeEditor.getContent(); // The Content
        
        if(key == ""){
          key = Date.today().toString("MMMM dS, yyyy");
        }
         
        lobby.app.save(key, value, function(data){
            if(data == "bad"){
              alert("Failed Saving Data");
            }else{
              lobby.app.ajax("saves.php", {}, function(data){
                 $(app + " #saves").html(data);
              });
              $(app).animate({backgroundColor: "rgb(101, 196, 53)"}, 1000, function(){
                $(app).animate({backgroundColor: "rgb(255, 255, 255)"}, 1000, function(){
                  $(app).attr("style", "");
                });
              });
            }
        });
      }
   });
  $(app+" #remove.btn").live("click", function(){
    var currentFile = $(app+" #saveName").val();
    if(currentFile == ""){
      $(app+" #error").text("This File is not saved.").fadeIn().delay(2000).fadeOut();
    }else{
      lobby.app.remove(currentFile, function(data){
        window.location = lobby.app.url;
      });
    }
  });
});
