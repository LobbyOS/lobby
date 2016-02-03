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
        \Lobby::addScript("Lobby.FS.filepicker", $this->dir . "/filepicker/js/filepicker.js");
        \Lobby::addStyle("Lobby.FS.filepicker", $this->dir . "/filepicker/css/filepicker.css");
      });
    }
  }

}
