lobby.load(function(){
  $(document).tooltip();
  
  $(".workspace ul:not(.pagination,.tabs)").addClass("collection");
  $(".workspace ul:not(.pagination,.tabs) li").addClass("collection-item");
});
