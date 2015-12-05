<?php
namespace Lobby\App;
class diary extends \Lobby\App {

  public function page($p){
    if(substr($p, 0, 7) == "/entry/"){
      $date = substr_replace($p, "", 0, 7);
      return $this->inc("/src/Page/entry.php", array(
        "entry_date" => $date
      ));
    }else{
      return "auto";
    }
  }
}
