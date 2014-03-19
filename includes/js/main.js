window.host="http://"+window.location.host;
window.opC=function(c, d, f){
 console.log(c[0]);
 c.live("click", function(){
  if(d.is(":hidden")){
   d.show();
  }else{
   d.hide();
  }
  if(typeof f =="function"){
   f();
  }
 });
};
$(document).mouseup(function (e){
 $(".c_c").each(function(i){
  if(!$(this).is(e.target) && $(this).find(e.target).length === 0 || e.target==$("body")[0]){
   $(this).hide();
  }
 });
});
window.ajaxRequest=function(name, fname, options, success){
 var tName="app_ajax.php";
 var options=$.extend(options, {"s7c8csw91" : name, "cx74e9c6a45" : fname});
 $.post(host+"/ajax/"+tName, options, function(data){
  if(typeof success == "function"){
   success(data);
  }
 });
};
