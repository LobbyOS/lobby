<?php
namespace Lobby\App;
class chatkin extends \Lobby\App {
  
  /**
   * Searching For Keys are faster than Values
   */
  public $networks = array();
  public $available_networks = array(
    "facebook" => 1,
    "google" => 1
  );
  public $connected = false;
  
  public function init(){
    require_once APP_DIR . "/src/inc/class.chat.php";
    
    foreach($this->available_networks as $network => $null){
      $connected = getData("network_". $network ."_connected");
      if($connected != null){
        $this->networks[$network] = 1;
      }
    }
    $this->connected = count($this->networks) != 0 ? true : false;
  }
  
  public function page(){
    $this->init();
    return "auto";
  }
  
  public function friendsCount(){
    $friends = 0;
    foreach($this->networks as $network => $null){
      $Network = "\\chatkin\\" . ucfirst($network);
      $n = new $Network;
      $friends = $friends + $n->friendsCount();
    }
    return $friends;
  }
  
  public function friends($start = 50){
    $start = ceil($start);
    $friends = array();
    
    foreach($this->networks as $network => $null){
      $Network = ucfirst($network);
      $Network = "\\chatkin\\" . ucfirst($network);
      $n = new $Network;
      $n->cookies = getData("network_". $network ."_cookies");
      $friends[$network] = $n->friends($start);
    }
  }
}
