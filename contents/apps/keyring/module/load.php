<?php
if(!\Lobby::status("lobby.admin")){
  \Lobby::addScript("keyring_module", L_URL . "/contents/apps/keyring/module/js/keyring.js");
}
