__AM=65521;
function cc(a){if("string"!=typeof a)throw Error("se");var b=1,c=0,d,e;for(e=0;e<a.length;e++)d=a.charCodeAt(e),b=(b+d)%__AM,c=(c+b)%__AM;return c<<16|b}
function tstamp(){return(new Date).getTime()}
$("#convertVideo").live("click", function(){
 videoURL=$("#videoURL").val();
 msg=$(".message");
 if(videoURL==""){
  msg.html("Please Enter A Video URL");
 }else{
  msg.html("Converting...");
  ajaxRequest("ymp3", "convert.php", {url:videoURL}, function(data){
   if(data=="0"){
    msg.html("Error");
   }else{
    data=JSON.parse(data);
    a=tstamp();
    tyu=a+"."+cc(data['id']+a);
    dwURL=curLoc+"/download.php?videoId="+data['id']+"&h="+data['h']+"&r="+tyu+"&name="+data['title'];
    msg.html("<div class='vidInfo'><div class='image'><img src='"+data['image']+"'/></div><div class='info'><div class='suc'>Video successfully converted to mp3</div>Title : "+data['title']+"<br/>Length : "+data['length']+"<div class='download'><a class='button' href='"+dwURL+"'>Download</a></div></div></div>");
   }
  });
 }
});
