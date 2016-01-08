function curDate() {
 t = new Date();
 function twoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
 }
 return t.getFullYear() + "-" + twoDigits(1 + t.getMonth()) + "-" + twoDigits(t.getDate()) + " " + twoDigits(t.getHours()) + ":" + twoDigits(t.getMinutes()) + ":" + twoDigits(t.getSeconds());
}

$(document).ready(function(){
   /* The App Parent Element URI */
   var app = ".workspace#ledit";
   
   $(app + " #save.button").live("click", function(){
      if($(app + " #text").val() == ""){
      $(app + " #error").text("Please Enter Something :-(").fadeIn().delay(2000).fadeOut();
      }else{
      key = $(app + " #saveName").val(); // The key Name
       value = $(app + " #text").val(); // The Content
       if(key == ""){
         key = curDate();
       }
       
      lobby.app.save(key, value, function(data){
          if(data == "bad"){
            alert("Failed Saving Data");
          }else{
            lobby.app.ajax("saves.php", {}, function(data){
               $(app + " #saves").html(data);
            });
            $(app + " #saved").fadeIn().delay(2000).fadeOut();
          }
      });
      }
   });
   $(app+" #remove.button").live("click", function(){
      var currentFile = $(app+" #saveName").val();
      if(currentFile == ""){
      $(app+" #error").text("This File is not saved.").fadeIn().delay(2000).fadeOut();
      }else{
      lobby.app.remove(currentFile, function(data){
          window.location = lobby.url + "/app/ledit";
      });
      }
   });
});