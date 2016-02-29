/**
 * For Debugging easily
 */
window.clog = function(msg){
   console.log(msg);
};

window.lobby = {
  /**
   * Array to store all callbacks
   */
  load_callbacks: [],
  
  /**
   * Add a callback when the page is loaded
   */
  load: function(callback){
    lobby.load_callbacks.push(callback);
  },
  
  /**
   * Call when Lobby is completely loaded
   */
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
        }, 225);
      }, false);
      d.getElementsByTagName('head')[0].appendChild(o);
    }
  },
  
  mod: {}
};

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
 * Make an AJAX Request
 */
lobby.ajax = function(fileName, options, callback, appID){
  /**
   * If the callback given is a function, use it
   * otherwise make a simple function that is of no use
   */
  callback = typeof callback == "function" ? callback : function(){};
  
  if(typeof options === "object"){
    var options = $.param(options);
  }
  
  /**
   * We give a s7 etc.. complicated name so that any fields
   * passed to this function doesn't have a field of same name.
   */
  if(appID != false){
    var options = $.param({"s7c8csw91": appID}) + "&" + options;
  }
  var options = $.param({"cx74e9c6a45": fileName, "csrf_token": lobby.csrf_token}) + "&" + options;

  var requestURL = lobby.url + "/includes/lib/lobby/ajax/ajax.php";
  $.post(requestURL, options, function(data){
    /**
     * On success, do callback function with the response data
     */
    callback(data);
  });
};

/* A Save Option frontend JS function that sends request to server to save a option */
lobby.saveOption = function(key, value, callback){
   /* If the callback given is a function, use it otherwise make a simple function that is of no use */
   var callback = typeof callback == "function" ? callback : function(){};
   var requestURL = lobby.url + "/includes/lib/lobby/ajax/saveOption.php";
   
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
