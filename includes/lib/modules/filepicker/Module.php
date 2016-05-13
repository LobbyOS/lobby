<?php
namespace Lobby\Module;

/**
 * FilePicker
 * ----------
 * A file picker to choose files from user's system
 */

class filepicker extends \Lobby\Module {

  public function init(){
    if(!\Lobby::status("lobby.assets-serve")){
      $this->addScript("filepicker.js");
      $this->addStyle("filepicker.css");
    }
  }

}
