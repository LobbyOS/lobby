<?php
namespace Lobby\App;

class pocket extends \Lobby\App {
  
  public function page($page){
    return "auto";
  }
  
  /**
   * +/- money
   */
  public function addItem($money){
    $money = (int) $money;
    if($money !== 0){
      \H::saveJSONData("sheet", $money);
    }
  }
  
  public function getBalance(){
    $sheet = \H::getJSONData("sheet");
    
    if(!empty($sheet)){
      var_dump($sheet);
    }else{
      return 0;
    }
  }
  
}
