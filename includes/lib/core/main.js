/* A temporary array for adding stuff */
window.tmp = {};
/* Merge extra values to the main lobby object */
window.lobby = jQuery.extend(window.lobbyExtra, window.lobby);

/* Get the current page URL */
lobby.curLoc = window.location.protocol + "//" + window.location.host + window.location.pathname;

/* A function that toggles display of an element from another element opC = OPenonClick */
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

/* Get the current App From URL ledit from lobby.dev/app/ledit */
lobby.curApp = function(){
 	path = lobby.curLoc.replace(lobby.host, "");
 	if(path.substr(0,1) == "/"){
 		path = path.slice(1); // Remove / at the end of URL
 	}
 	
 	/* Check if it's an App page */
 	if(path.substr(0,4) == "app/"){
  		path = path.replace("app\/", "");
  		if(path == ""){
   		return false;
  		}else{
   		return path.match("/") != null ? path.split("/")[0] : path; // Return the app ID
  		}
 	}else{
  		return false;
 	}
};

/* Make an AJAX Request to an app */
lobby.ajaxRequest = function(fileName, options, callback, appID){
 	var appID = typeof appID != 'undefined' ? appID : lobby.curApp(); // If App ID is not given, get it from the URL if the page is an app page
 	
 	/* If the callback given is a function, use it otherwise make a simple function that is of no use */
 	var callback   = typeof callback == "function" ? callback : function(){};
 	var requestURL = lobby.host + "/includes/lib/core/ajax" + "/app.php";
 
 	/* We give a s7 etc.. unique name so that any fields sent using this function doesn't have a duplicate field. */
 	var options = $.extend(options, {"s7c8csw91": appID, "cx74e9c6a45": fileName});
 
 	$.post(requestURL, options, function(data){
  		if(typeof callback == "function"){
			/* On success, do callback function with the response data */
			callback(data);
  		}
 	});
};

/* A Save Data frontend JS function that sends request to server to save the data */
lobby.saveData = function(key, value, callback, appID){
 	var appID = typeof appID !== 'undefined' ? appID : lobby.curApp(); // If App ID is not given, get it from the URL if the page is an app page
 
 	/* If the callback given is a function, use it otherwise make a simple function that is of no use */
 	var callback   = typeof callback == "function" ? callback : function(){};
 	var requestURL = lobby.host + "/includes/lib/core/ajax/saveData.php";
 	
 	if(appID == "" || key == "" || value == "" || appID === false){
  		callback("bad");
 	}else{
  		$.post(requestURL, {"appId": appID, "key": key, "value": value}, function(data){
   		callback(data);
  		}).error(function(){
   		/* AJAX Request wasn't successful, so make sure the callback is alerted of the error. */
   		callback("bad");
  		});
 	}
};

/* A Remove Data frontend JS function that sends request to server to remove the data  */
lobby.removeData = function(key, callback, appID){
 	var appID = typeof appID !== 'undefined' ? appID : lobby.curApp(); // If App ID is not given, get it from the URL if the page is an app page
 
 	/* If the callback given is a function, use it otherwise make a simple function that is of no use */
 	var callback   = typeof callback == "function" ? callback : function(){};
	var requestURL = lobby.host + "/includes/lib/core/ajax/removeData.php";

 	if(appID == "" || key == "" || appID === false){
  		callback("bad");
 	}else{
  		$.post(requestURL, {"appId": appID, "key": key}, function(data){
			callback(data);
  		}).error(function(){
			/* AJAX Request wasn't successful, so make sure the callback is alerted of the error. */
			callback("bad");
  		});
 	}
};

/* A Save Option frontend JS function that sends request to server to save a option */
lobby.saveOption = function(key, value, callback){
 	/* If the callback given is a function, use it otherwise make a simple function that is of no use */
 	var callback   = typeof callback == "function" ? callback : function(){};
 	var requestURL = lobby.host + "/includes/lib/core/ajax/saveOption.php";
 	
 	if(key == "" || value == ""){
  		callback("bad");
 	}else{
  		$.post(requestURL, {"key": key, "value": value}, function(data){
			callback(data);
  		}).error(function(){
			/* AJAX Request wasn't successful, so make sure the callback is alerted of the error. */
			callback("bad");
  		});
 	}
};

/* For Debugging easily */
window.clog=function(msg){
 	console.log(msg);
};