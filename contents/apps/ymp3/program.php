<?php
class ymp3 extends \Lobby\App{
  
  public function page($page){
    if($page == "/"){
      return $this->indexPage();
    }
  }
  
  public function indexPage(){
    $this->addStyle("cdn/main.css");
    $this->addScript("cdn/main.js");
    
    return array(APP_DIR . "/page-index.php");
  }
}
?>