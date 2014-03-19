$(document).ready(function(){
 var app=".workspace#ledit";
 $(app+" #save.button").live("click", function(){
  if($(app+" #text").val()==""){
   $(app+" #error").text("Please Enter Something :-(").fadeIn().delay(2000).fadeOut();
  }else{
   ajaxRequest("ledit", "ajax/save.php",{text:$(app+" #text").val(), name:$(app+" #saveName").val()}, function(data){
    ajaxRequest("ledit", "ajax/saves.php",{}, function(data){
     $(app+" #saves").html(data);
    });
    $(app+" #saved").fadeIn().delay(2000).fadeOut();
   });
  }
 });
 $(app+" #remove.button").live("click", function(){
  var currentFile=$(app+" #saveName").val();
  if(currentFile==""){
   $(app+" #error").text("This File is not saved.").fadeIn().delay(2000).fadeOut();
  }else{
   ajaxRequest("ledit", "ajax/remove.php",{id:currentFile}, function(data){
    window.location="<[host]>/app/ledit";
   });
  }
 });
});
