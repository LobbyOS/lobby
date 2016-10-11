lobby.notify = {
  box: null,

  checkInterval: null,
  nfs: [],

  init: function(){
    lobby.notify.box = $("nav #notifyBox");
    this.update();
    this.events();
    this.checkInterval = setInterval(function(){
      lobby.notify.update();
    }, 30000);
  },

  /**
   * The theme should define these
   */
  push: function(){},
  onNewItems: function(){},

  update: function(){
    lobby.ar("includes/lib/lobby/ar/notify.php", {}, function(response){
      nfs = JSON.parse(response); // Short for notifications
      if(nfs.length === 0){
        lobby.notify.box.html("<center><h4>No Notifications</h4></center>");
      }else{
        $.each(nfs, function(id, notification){
          notification["id"] = id;
          lobby.notify.nfs.push(id);
          lobby.notify.push(notification);
        });

        /**
         * Check if notify items have been removed
         */
        if(lobby.notify.nfs.length !== nfs.length){
          $.each(lobby.notify.nfs, function(i, notifyID){
            if(typeof nfs[notifyID] === "undefined"){
              lobby.notify.push({
                "id": notifyID,
                "removed": true
              });
            }
          });
        }

        lobby.notify.onNewItems();
      }
    });
  },

  events: function(){

  }

};

/**
 * On Lobby Init
 */
lobby.load(function(){
  lobby.notify.init();
});
