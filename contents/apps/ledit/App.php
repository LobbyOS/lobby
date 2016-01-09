<?php
namespace Lobby\App;
class ledit extends \Lobby\App {
  
  public function page($page){
    return $this->indexPage();
  }
  
  public function indexPage(){
    return "auto";
  }
}
?>
