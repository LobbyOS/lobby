<?php
namespace Lobby\Module;

/**
 * Adds more functionality to \Lobby\FS
 * Mainly to UI
 */

class FileSystem extends \Lobby\Module {

  public function init(){
    if(!\Lobby::status("lobby.serve")){
      \Lobby::hook("head.begin", function(){
        \Lobby::addScript("Lobby.FS.filechooser", $this->dir . "/js/filechooser.js");
        \Lobby::addStyle("Lobby.FS.filechooser", $this->dir . "/css/filechooser.css");
      });
    }
  }

}
