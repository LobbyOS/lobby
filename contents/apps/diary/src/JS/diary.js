lobby.app.saveDiary = function(){
  data = $(".diary .entry").html().replace(/<p(.*?)>(.*?)<\/p>/g, '[p]$2[/p]');
  
  if($(".diary .entry").text() != " Type here... "){
    lobby.app.save(lobby.app.date, data, function(){
      $(".diary .paper").animate({backgroundColor: "rgb(101, 196, 53)"}, 1000, function(){
        $(".diary .paper").animate({backgroundColor: "rgb(255, 255, 255)"}, 1000, function(){
          $(".diary .paper").attr("style", "");
        });
      });
    });
    lobby.app.ajax("date.php", {date: lobby.app.date});
  }else{
    alert("Please write something....");
  }
};
$(".workspace #save_diary").live("click", function(){
  lobby.app.saveDiary();
});

setInterval(function(){
  lobby.app.saveDiary();
}, 30000);

$(function(){
  $(".paper .entry").notebook({
    placeholder: "Type here...",
    modifiers: ['bold', 'italic', 'underline', 'anchor']
  });
});
