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
  
  mod: {},
  notify: {}
};

if(typeof window.lobbyExtra != "undefined"){
  window.lobby = $.extend(window.lobbyExtra, lobby);
}

$(function(){
  lobby.load_init();
});

/**
 * Get the current page URL
 */
lobby.curLoc = window.location.protocol + "//" + window.location.host + window.location.pathname;

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
  var options = $.param({"cx74e9c6a45": fileName, "csrf_token": lobby.csrfToken}) + "&" + options;

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

/**
 * Show short messages as popup
 * From https://github.com/Dogfalo/materialize/blob/master/js/toasts.js
 */
lobby.toast = function (message, displayLength, className, completeCallback) {
    Vel = function(html, css, options){
      $(html).animate(css, options);
    }
    className = className || "";

    var container = document.getElementById('toast-container');

    // Create toast container if it does not exist
    if (container === null) {
        // create notification container
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    // Select and append toast
    var newToast = createToast(message);

    // only append toast if message is not undefined
    if(message){
        container.appendChild(newToast);
    }

    newToast.style.top = '35px';
    newToast.style.opacity = 0;

    // Animate toast in
     Vel(newToast, { "top" : "0px", opacity: 1 }, {duration: 300,
      easing: 'easeOutCubic',
      queue: false});

    // Allows timer to be pause while being panned
    var timeLeft = displayLength;
    var counterInterval = setInterval (function(){


      if (newToast.parentNode === null)
        window.clearInterval(counterInterval);

      // If toast is not being dragged, decrease its time remaining
      if (!newToast.classList.contains('panning')) {
        timeLeft -= 20;
      }

      if (timeLeft <= 0) {
        // Animate toast out
        Vel(newToast, {"opacity": 0, marginTop: '-40px'}, { duration: 375,
            easing: 'easeOutExpo',
            queue: false,
            complete: function(){
              // Call the optional callback
              if(typeof(completeCallback) === "function")
                completeCallback();
              // Remove toast after it times out
              this[0].parentNode.removeChild(this[0]);
            }
          });
        window.clearInterval(counterInterval);
      }
    }, 20);



    function createToast(html) {

        // Create toast
        var toast = document.createElement('div');
        toast.classList.add('toast');
        if (className) {
            var classes = className.split(' ');

            for (var i = 0, count = classes.length; i < count; i++) {
                toast.classList.add(classes[i]);
            }
        }
        // If type of parameter is HTML Element
        if ( typeof HTMLElement === "object" ? html instanceof HTMLElement : html && typeof html === "object" && html !== null && html.nodeType === 1 && typeof html.nodeName==="string"
) {
          toast.appendChild(html);
        }
        else if (html instanceof jQuery) {
          // Check if it is jQuery object
          toast.appendChild(html[0]);
        }
        else {
          // Insert as text;
          toast.innerHTML = html; 
        }
        // Bind hammer
        var hammerHandler = new Hammer(toast, {prevent_default: false});
        hammerHandler.on('pan', function(e) {
          var deltaX = e.deltaX;
          var activationDistance = 80;

          // Change toast state
          if (!toast.classList.contains('panning')){
            toast.classList.add('panning');
          }

          var opacityPercent = 1-Math.abs(deltaX / activationDistance);
          if (opacityPercent < 0)
            opacityPercent = 0;

          Vel(toast, {left: deltaX, opacity: opacityPercent }, {duration: 50, queue: false, easing: 'easeOutQuad'});

        });

        hammerHandler.on('panend', function(e) {
          var deltaX = e.deltaX;
          var activationDistance = 80;

          // If toast dragged past activation point
          if (Math.abs(deltaX) > activationDistance) {
            Vel(toast, {marginTop: '-40px'}, { duration: 375,
                easing: 'easeOutExpo',
                queue: false,
                complete: function(){
                  if(typeof(completeCallback) === "function") {
                    completeCallback();
                  }
                  toast.parentNode.removeChild(toast);
                }
            });

          } else {
            toast.classList.remove('panning');
            // Put toast back into original position
            Vel(toast, { left: 0, opacity: 1 }, { duration: 300,
              easing: 'easeOutExpo',
              queue: false
            });

          }
        });

        return toast;
    }
};
