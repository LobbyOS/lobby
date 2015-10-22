<?php
namespace Lobby\Modules;

class app_keyring extends \Lobby\Module {

  public function init(){
    if(!\Lobby::status("lobby.admin")){
      \Lobby::addScript("keyring_module", L_URL . "/contents/apps/keyring/module/js/keyring.js");
    }
  }

}
