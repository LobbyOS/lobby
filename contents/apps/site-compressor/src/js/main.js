lobby.app.saves = {};

/* Make an associative array of Forms' data */
$.fn.serializeHTML = function() {
    var formData = "";
    var formId = $(this).attr("id");
    this.find('[name]').each(function() {
        name = $(this).attr("name");
        if(name.substr(-2, 2) == "[]"){
          name = name.substr(0, name.length-2);   
          formData+='<input name="'+ formId + "[" + name + "][]" + '" value="' + $(this).val() + '" type="text" />';
        }else{
          if($(this).is(":checked")){
            formData += '<input name="'+ formId + "[" + name + "]" + '" type="checkbox" checked="checked" />';
          }else if($(this).attr("type") != "checkbox"){
            formData += '<input name="'+ formId + "[" + name + "]" + '" value="' + $(this).val().replace(/"/g, "'") + '" type="text" />';
          }
        }
    });
    return formData;
};

/**
 * Restoring/Loading Configuration
 */
lobby.app.restoreConfig = function(configName){
 /**
  * Get the config as JSON object
  */
  $replaceData = lobby.app["saves"][configName]["replacer"];
  $mainData = lobby.app["saves"][configName]["main"];
 
  /* Replacer Config */
  var replaceFields={};
  $("#replaceFields div").remove();
  $.each($replaceData, function(key, val){
     $(".workspace#site-compressor .addReplaceField").click();
     $(".workspace#site-compressor #replaceFields div:last").find("[name='replaceFrom[]']").val(key);
     $(".workspace#site-compressor #replaceFields div:last").find("[name='replaceTo[]']").val(val);
  });
   
   /* Restore Site Details */
  $('.workspace#site-compressor .top [data-binding]').each(function(){ 
    // handle the set value
    // need consider the different value type for different field type
    var $this = $(this);
    var val = $mainData[$this.data('binding')];
  
     // for chechbox
    if($this.is('[type=checkbox]')){
      $this.prop('checked',val)
      // for others
    }else{
      $this.val(
       decodeURIComponent(
         val.replace($this.attr("name") + "=", "")
       )
      );
    }
  });
  $(".workspace#site-compressor [name='beforeCommand'], .workspace#site-compressor [name='afterCommand']").each(function(){
    $(this).val($(this).val().replace(/\+/g, " "));
  });
};

/* Save Configuration */
lobby.app.saveConfig = function(configName){
   /* Save Site Details & Compression Options */
    var generalSettings = {};
  $('.workspace#site-compressor .top [data-binding]').each(function(){
    generalSettings[$(this).data('binding')] = $(this).serialize();
  });
    
    /* Since Replacer may have mutiple fields according to user choice, we add each values one by one. */
    var replacerData = {};
    if($(".workspace#site-compressor #siteDetails div").length != 0){
      $(".workspace#site-compressor #siteDetails div").each(function(){
        from = $(this).find("[name='replaceFrom[]']").val();
        to = $(this).find("[name='replaceTo[]']").val();
        if(from!="" && to!=""){
          replacerData[from] = to;
        }
      });
    }
  var configData = JSON.stringify({
    "main"      : generalSettings,
    "replacer" : replacerData
  });
    lobby.app.save(configName, configData, function(data){
      if(data == "bad"){
      alert("Failed Saving Data");
    }else{
        lobby.app.displaySaves();
      }
  });
}

lobby.app.displaySaves = function(){
  lobby.app.ajax("saves.php", {}, function(data){
    var data = JSON.parse(data);
    if( data.length == 0 ){
      $(".workspace#site-compressor #configSaves").html("No Saves Found");
    }else{
      $(".workspace#site-compressor #configSaves").html(""); // Empty the saves
      
      $.each(data, function(key){
        $(".workspace#site-compressor #configSaves").append("<tr><td>" + key + "</td><td><a href='javascript:void(0);' id='" + key + "' class='loadConfig button blue'>Load</a><a href='javascript:void(0);' id='" + key + "' class='removeConfig button red'>Remove</a></td></tr>");
      });
    }
    lobby.app.saves = data;
  });
};

lobby.load(function(){
  localStorage["lastSaveName"] = "";
 
  /* Add Dynamic Srollbars */
  $(".workspace#site-compressor #left, .workspace#site-compressor #right, .workspace#site-compressor .compress-status").addClass("scrollbar-inner").scrollbar();
  $(".workspace#site-compressor .table #left:first").width($(document).width() * 40/100);
  $(".workspace#site-compressor .table #right:first").width($(document).width() * 57/100);
 
  /* Display the saved configs */
  lobby.app.displaySaves();
 
  /* Load Config when requested */
  $(".workspace#site-compressor .loadConfig").live("click", function(){
    id = $(this).attr("id");
    localStorage["lastSaveName"] = id;
    lobby.app.restoreConfig(id);
    $(".workspace .left #configSaves tr").attr("style", "");
    $(this).parents("tr").css("background-color", "#FAEBD7");
  });
 
  /* Remove Config when requested */
  $(".workspace#site-compressor .removeConfig").live("click", function(){
    configName = $(this).attr("id");
    lobby.app.remove(configName, function(){
      /* After deletion, Display the saves that are left */
      lobby.app.displaySaves();
    });
  });
  
  /**
   * Quick Action Compress
   */
  $(".workspace#site-compressor #startCompress").live("click", function(){
    $(".workspace#site-compressor #siteDetails").submit();
  });
 
  $(".workspace#site-compressor #siteDetails").live("submit", function(e){
    e.preventDefault();
    var formURL = lobby.app.src + "/src/ajax/compress.php";
  
    /* Make the fake form to be inserted in iframe */
    var formHTML = $("<form>").html(
      $(".workspace#site-compressor #options").serializeHTML() + $(".workspace#site-compressor #siteDetails").serializeHTML()
    ).attr({
      "method" : "POST",
      "action" : formURL
    }).css("display", "none").wrap('<p>').parent().html();
  
    $(".workspace#site-compressor .compress-status").html("");
    $("<iframe/>").attr({
      "height"      : ($(document).height() - ($(".workspace#site-compressor .top").offset().top + $(".workspace#site-compressor .top").height())) - 25,
      "width"       : "100%",
      "style"       : "margin: 10px 0;",
      "frameborder" : 0,
    }).appendTo(".workspace#site-compressor .compress-status");
    $(".workspace#site-compressor .compress-status").find("iframe").contents().find("body").html(formHTML);
    $(".workspace#site-compressor .compress-status").find("iframe").contents().find("body").find("form").submit();
  });
  $(".workspace#site-compressor .addReplaceField").live("click", function(){
    $(this).before("<div style='margin:5px;'><input name='replaceFrom[]' placeholder='From'><input name='replaceTo[]' style='margin-left:5px' placeholder='To'/></div>");
  });
 
  $(".workspace#site-compressor #saveConfig").live("click", function(){
    var saveName = prompt("Please type in the name of the configuration", localStorage["lastSaveName"]);
    if(saveName != null){ /* If user didn't clicked cancel button */
      if(saveName.match("'")){
        alert("Can't have special characters in the save name");
      }else{
        if(saveName == ""){
          saveName = "default";
        }
        localStorage["lastSaveName"] = saveName;
        lobby.app.saveConfig(saveName);
      }
    }
  });
  
  /**
   * Site path Picker
   */
  $(".workspace #choose_site_path").live("click", function(){
    lobby.mod.FilePicker("/", function(result){
      $(".workspace #site_location").val(result.dir);
    });
  });
  
  $(".workspace #choose_site_output_path").live("click", function(){
    lobby.mod.FilePicker("/", function(result){
      $(".workspace #site_output_location").val(result.dir);
    });
  });
});
