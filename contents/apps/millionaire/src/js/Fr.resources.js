/**
 * Francium Resources plugin 0.3 (31st May 2015)
 * Copyright Subin Siby - http://subinsb.com
 * 
 * ------------------------------------------------------------
 * Apache Licensed - http://www.apache.org/licenses/LICENSE-2.0
 * ------------------------------------------------------------
 * 
 * A JavaScript plugin to load assets in a web page ASYNCHRONOUSLY.
 * Helps to also show progress bar of assets being loaded in the webpage.
 *
 * Full Documentation & Support - http://subinsb.com/html5-record-mic-voice
*/
 
(function(){
  window.Fr = window.Fr || {};
  
  Fr.resources = {
    assets : {},
    finished: {},
    percent_called: {},
    loaded_callback : function(){},
    
    push: function(name, src){
      if(typeof name == "object"){
        for (var i in name){
          item_src = name[i];
          this.assets[i] = item_src;
        }
      }else{
        this.assets[name] = src;
      }
    },
    
    progress: function(){},
    
    load: function(callback){
      this.progress(0, 0, Object.keys(Fr.resources.assets).length);
      this.loadAsset(0);
      
      this.loaded_callback = callback;
    },
    
    loadAsset: function(i){
      if(typeof this.assets[Object.keys(this.assets)[i]] != "undefined"){
        asset = Object.keys(Fr.resources.assets)[i];
        src = this.assets[Object.keys(this.assets)[i]];
        
        this.xhr(i, asset, src);
      }
    },
    
    xhr: function(id, asset, src){
      var request = new XMLHttpRequest();
      request.onprogress = function(e){
        percent = Math.round((e.loaded/e.total) * 100);
        
        finished_assets = Object.keys(Fr.resources.finished).length;
        total_assets = Object.keys(Fr.resources.assets).length;
        assets_percent = parseFloat( (finished_assets/total_assets) * 100 );
        asset_percent_allocation = Math.round(100 / total_assets);
        
        if(typeof Fr.resources.percent_called[percent] == "undefined"){
          Fr.resources.percent_called[percent] = 1;
          
          new_assets_percent = Math.round( ((percent * Math.abs(asset_percent_allocation - assets_percent)) / 100) + assets_percent );
          Fr.resources.progress(new_assets_percent, finished_assets, total_assets);
        }
      };
        
      request.onreadystatechange = function(e){
        if(request.readyState == 4 && typeof Fr.resources.finished[asset] == "undefined"){
          Fr.resources.finished[asset] = src;
          finished_assets = Object.keys(Fr.resources.finished).length;
          total_assets = Object.keys(Fr.resources.assets).length;
          assets_percent = Math.round((finished_assets/total_assets) * 100);

          Fr.resources.progress(assets_percent, finished_assets, total_assets);
          Fr.resources.percent_called = {};
          
          if(finished_assets == total_assets){
            Fr.resources.loaded_callback();
          }else{
            // Load next asset
            Fr.resources.loadAsset(parseFloat(id) + 1);
          }
        }
      };
        
      request.open("GET", src, true);
      request.send();
    }
  };
})(window);
