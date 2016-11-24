/**
 * Handle app installation Asynchronously
 * @param int id - The App ID to install
 * @param obj area - The jQuery object where install progress is shown
 */
lobby.installApp = function(id, area){
  var retryIntervalStarted = 0;
  var startRetryCountdown = function(){
    if(retryIntervalStarted == 0){
      setInterval(function(){
        if($("#retryInstallCountdown:last").text() == "0"){
          area.html('');
          check();
        }else{
          $("#retryInstallCountdown").text(parseInt($("#retryInstallCountdown").text()) - 1);
        }
      }, 1000);
      retryIntervalStarted = 1;
    }
  };

  var check = function(){
    lobby.ar("/admin/install-app", {"id": id}, function(r){
      r = JSON.parse(r);

      if(r.statusID == "error"){
        html = "<li class='collection-item' style='color: red;' data-status-id='error'>"+ r.status +"<br/>Will try again in <span id='retryInstallCountdown'>20</span> seconds.<cl/><a id='#retryInstallNow' class='btn green'>Try Again Now</a><a href='"+ lobby.url +"/admin/lobby-store.php?app="+ id +"' class='btn red'>Cancel</a></li>";
        startRetryCountdown();
      }else{
        html = "<li class='collection-item' data-status-id='"+ r.statusID +"'>"+ r.status +"</li>";
        if(r.statusID !== "install_finished"){
          setTimeout(check, 1000);
        }
      }
      if(area.find("li:last").attr("data-status-id") == r.statusID){
        area.find("li:last").replaceWith(html);
      }else{
        area.append(html);
      }
    });
  };
  check();

  $("#retryInstallNow").live("click", function(){
    check();
  });
};
