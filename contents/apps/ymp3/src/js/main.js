$.extend(lobby.app, {

  init: function(){
    this.bind_events();
  },

  convert: function(videoURL){
    lobby.app.ajax("convert.php", {"url" : videoURL}, function(data){
      if(data === ""){
        msg.html("Error");
      }else{
        data = JSON.parse(data);
        if(data.hash != null && data.error == ""){
          if(data.sid === "0"){
            msg.html("Please wait while the server converts the video...<a clear id='refresh' data-videoID='"+ videoURL +"'>Refresh Status</a>");
            setTimeout(function(){
              lobby.app.convert(videoURL);
            });
          }else{
            dwURL = lobby.app.src + "/src/ajax/download.php?hash=" + data.hash + "&s=" + data.sid;
            
            msg.html("<div class='vidInfo'><div class='title'>"+ data['title'] +"</div><div class='download'><a class='button red' id='downloadURL' href='" + dwURL + "'>Download</a></div></div></div>");
          }
        }else{
          msg.html("Error");
        }
      }
    });
  },
  
  bind_events: function(){
    $(".workspace #downloadURL").live("click", function(e){
      e.preventDefault();
      url = $(this).attr("href");
      $("<iframe src='"+ url +"'></iframe>").css("display", "none").appendTo("body");
    });
    
    $(".workspace #convertVideo").live("click", function(){
      videoURL = $("#videoURL").val();
      msg = $(".message");
      if(videoURL == ""){
        msg.html("Please Enter A Video URL");
      }else{
        msg.html("Converting...");
        lobby.app.convert(videoURL);
      }
    });
    
    $(".workspace #refresh").live("click", function(){
      lobby.app.convert($(this).data("videoID"));
    });
  }
});
lobby.app.init();
