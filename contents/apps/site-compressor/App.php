<?php
class site_compressor extends \Lobby\App {
  public function page($page){
    if( $page == "/site" ){
      $this->setTitle("Compress A Site");
      
      $this->addStyle("main.css");
      $this->addStyle("scrollbar.css");
      $this->addScript("scrollbar.js");
      $this->addScript("main.js");
      
      return $this->inc("/src/Page/site.php");
    }elseif( $page == "/html" || $page == "/css" || $page == "/js" ){
      $this->setTitle("Compress ". strtoupper(substr($page, 1)));
      return "auto";
    }else{
      return "auto";
    }
  }
}
?>
