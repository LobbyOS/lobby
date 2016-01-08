<?php
namespace Lobby\App;
class ledit extends \Lobby\App {
  
  public function page($page){
    return $this->indexPage();
  }
  
  public function indexPage(){
    $this->addStyle("main.css");
    $this->addScript("main.js");
    $html = $this->inc("/src/page/index.php");
    
    /* We obtain the save name from page-index.php */
    if(isset($id)){
      $this->setTitle($id);
    }
    
    return $html;
  }
}
?>
