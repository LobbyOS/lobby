<?php
namespace Lobby\Modules;

/**
 * Adds more functionality to \Lobby\FS
 * Mainly to UI
 */

class FileSystem extends \Lobby\Module {

  public function init(){
    if(!\Lobby::status("lobby.serve")){
      \Lobby::hook("head.begin", function(){
        \Lobby::addScript("Lobby.FS.filechooser", $this->dir . "/JS/filechooser.js");
        \Lobby::addStyle("Lobby.FS.filechooser", $this->dir . "/CSS/filechooser.css");
      });
    }
  }

}
