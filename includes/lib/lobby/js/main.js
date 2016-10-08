/**
 * For Debugging easily
 */
window.clog = function(msg){
   console.log(msg);
};

window.lobby = {

  /**
   * Check if Lobby is accessed with a mobile device
   */
  isMobile: function(){
    /**
     * browser.mobile (http://detectmobilebrowser.com/)
     */
    a = navigator.userAgent||navigator.vendor||window.opera;
    return /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4));
  },

  /**
   * Array to store all callbacks
   */
  load_callbacks: [],

  /**
   * ASYNC scrpts loaded
   * @type {Number}
   */
  load_count: 0,

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
        $.each(lobby.load_callbacks, function(i, callback){
          callback();
        });
        lobby.load_callbacks = [];
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
 * Make an Asynchornous Request
 */
lobby.ar = function(fileName, options, callback, appID){
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
  var options = $.param({"cx74e9c6a45": fileName, "csrfToken": lobby.csrfToken}) + "&" + options;

  var requestURL = lobby.url + "/includes/lib/lobby/ar/ajax.php";
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
    $.post(requestURL, {"key": key, "value": value, "csrfToken": lobby.csrfToken}, function(data){
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
