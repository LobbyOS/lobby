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
  
  public function makePanelTree($id, $item){
    $html = isset($item['html']) ? $item['html'] : substr($this->makePanelItem($item['text'], $item['href'], $id, "prnt"), 0, -5);
      $html .= "<ul>";
      foreach($item['subItems'] as $itemID => $subItem){
        $html .= $this->makePanelItem($subItem['text'], $subItem['href'], $itemID);
      }
      $html .= "</ul>";
    $html .= "</li>";
    return $html;
  }
  
  public function makePanelItem($text, $href, $id, $extraClass = ""){
    if($href == L_URL){
      /**
       * Home button
       */
      $html = "<li class='item home'><a href='". L_URL ."' class='$extraClass'></a></li>";
    }else if($href == "/admin"){
      /**
       * Admin button
       */
      $html = "<li class='item lobby'><a href='". \Lobby::u($href) ."' class='$extraClass'>Lobby</a></li>";
    }else{
      $html = '<li class="item ' . $extraClass . '" id="' . $id . '">';
        if($href == ""){
          $html .= $text;
        }else{
          $html .= $href == "htmlContent" ? $text : \Lobby::l($href, $text);
        }
      $html .= '</li>';
    }
    return $html;
  }
  
}
