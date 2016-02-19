<?php
namespace Lobby\Module;

/**
 * FilePicker
 * ----------
 * A file picker to choose files from user's system
 */

class filepicker extends \Lobby\Module {

  public function init(){
    if(!\Lobby::status("lobby.serve")){
      \Lobby::hook("head.begin", function(){
        \Lobby::addScript("Lobby.filepicker", $this->dir . "/js/filepicker.js");
        \Lobby::addStyle("Lobby.filepicker", $this->dir . "/css/filepicker.css");
      });
    }
  }

}
