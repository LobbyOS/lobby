/**
 * For Debugging easily
 */
window.clog = function(msg){
   console.log(msg);
};

if(typeof lobby == "undefined"){
  window.lobby = {
    load_callbacks: [],
    load: function(callback){
      lobby.load_callbacks.push(callback);
    },
    load_init: function(){
      if(typeof lobby.load_script_url != "undefined"){
        var d = document, o = d.createElement('script');
        o.type = "text/javascript";
        o.async = true;
        o.src = lobby.load_script_url;
        o.addEventListener('load', function (e){
          setTimeout(function(){
            $.each(lobby.load_callbacks, function(i, callback){
              callback();
            });
            lobby.load_callbacks = [];
          }, 10);
        }, false);
        d.getElementsByTagName('head')[0].appendChild(o);
      }
    },
    
    mod: {}
  };
}
if(typeof lobbyExtra != "undefined"){
  window.lobby = $.extend(lobbyExtra, lobby);
}

$(function(){
  lobby.load_init();
});

/**
 * Get the current page URL
 */
lobby.curLoc = window.location.protocol + "//" + window.location.host + window.location.pathname;

/**
 * A function that toggles display of an element from another element opC = OpenonClick
 */
lobby.opC = function(trigger, elem, callback){
  trigger.live("click", function(){
    if(elem.is(":hidden")){
      elem.show();
    }else{
      elem.hide();
    }
    if(typeof callback =="function"){
      callback();
    }
  });
};

/* If an overlay element is visible and mouse clicks outside of it, hide that overlay element */
$(document).mouseup(function (e){
  $(".c_c").each(function(i){
    if(!$(this).is(e.target) && $(this).find(e.target).length === 0 || e.target==$("body")[0]){
      $(this).hide();
    }
  });
});

/**
 * Make an AJAX Request to an app
 */
lobby.ajax = function(fileName, options, callback, appID){
  /**
   * If the callback given is a function, use it
   * otherwise make a simple function that is of no use
   */
  var callback = typeof callback == "function" ? callback : function(){};
  
  /**
   * If the App ID is not defined, then it is a direct contact
   * to the AJAX file in /includes/lib/core/Ajax
   */
  if(appID === false){
    var requestURL = lobby.url + "/includes/lib/core/Ajax/" + fileName;
  }else{
    var requestURL = lobby.url + "/includes/lib/core/Ajax" + "/app.php";
  }
  
  if(typeof options == "object"){
    options = $.param(options);
  }
  
  /**
   * We give a s7 etc.. complicated name so that any fields
   * passed to this function doesn't have a field of same name.
   */
  if(appID != false){
    var options = $.param({"s7c8csw91": appID, "cx74e9c6a45": fileName}) + "&" + options;
  }
  var options = $.param({"csrf_token": lobby.csrf_token}) +"&"+ options;
 
  $.post(requestURL, options, function(data){
    if(typeof callback == "function"){
      /**
       * On success, do callback function with the response data
       */
      callback(data);
    }
  });
};

/* A Save Option frontend JS function that sends request to server to save a option */
lobby.saveOption = function(key, value, callback){
   /* If the callback given is a function, use it otherwise make a simple function that is of no use */
   var callback = typeof callback == "function" ? callback : function(){};
   var requestURL = lobby.url + "/includes/lib/core/Ajax/saveOption.php";
   
   if(key == "" || value == ""){
      callback("bad");
   }else{
    $.post(requestURL, {"key": key, "value": value, "csrf_token": lobby.csrf_token}, function(data){
      callback(data);
    }).error(function(){
      /**
       * AJAX Request wasn't successful, so make sure the callback is alerted of the error.
       */
      callback("bad");
    });
   }
};

/**
 * Redirect
 */
lobby.redirect = function(path){
  window.location = lobby.url + path;
};

/*
$("a[href]").die("click").live("click", function(e){
  url = $(this).attr("href");
  if( url.match(lobby.url) ){
    e.preventDefault();
    $(".panel #ajaxLoader").css("width", "100%");
    setTimeout(function(){
      window.location = url;
    }, 500);
  }else{
    return true;
  }
});*/
