<?php
namespace Lobby\UI\Themes;

class hine extends \Lobby\UI\Theme {
  
  /**
   * Called before panel is made
   */
  public function panel($isAdmin){
    $this->addStyle("/Panel/CSS/panel.css");
    $this->addScript("/Panel/JS/superfish.js");
    $this->addScript("/Panel/JS/panel.js");
  }
  
  /**
   * Include stuff for designing dashboard
   */
  public function dashboard(){
    $this->addScript("/Dashboard/JS/metrojs.js");
    $this->addStyle("/Dashboard/CSS/metrojs.css");
    $this->addScript("/Dashboard/JS/scrollbar.js");
    $this->addStyle("/Dashboard/CSS/scrollbar.css");
    $this->addScript("/Dashboard/JS/jquery.contextmenu.js");
    $this->addStyle("/Dashboard/CSS/jquery.contextmenu.css");
    $this->addScript("/Dashboard/JS/dashboard.js");
    $this->addStyle("/Dashboard/CSS/dashboard.css");
  }
  
}
