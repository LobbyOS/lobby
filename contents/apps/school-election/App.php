<?php
namespace Lobby\App;
/**
 * School Election
 * - Subin Siby
 */
class school_election extends \Lobby\App {
  
  public $config = array();
  
  public function page($p){
    $this->config = json_decode($this->get("/src/Data/config.json"), true);
    $this->addStyle("style.css");
    return "auto";
  }

}
