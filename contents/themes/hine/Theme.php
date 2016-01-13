<?php
namespace Lobby\UI\Themes;

class hine extends \Lobby\UI\Theme {
  
  public function init(){
    $this->addScript("/src/lib/material-design/materialize.js");
    $this->addStyle("/src/lib/material-design/materialize.css");
  }
  
  /**
   * Called before panel is made
   */
  public function panel($isAdmin){
    $this->addStyle("/src/panel/css/panel.css");
    $this->addScript("/src/panel/js/superfish.js");
    $this->addScript("/src/panel/js/panel.js");
  }
  
  /**
   * Include stuff for designing dashboard
   */
  public function dashboard(){
    $this->addScript("/src/dashboard/js/scrollbar.js");
    $this->addStyle("/src/dashboard/css/scrollbar.css");
    $this->addScript("/src/dashboard/js/jquery.contextmenu.js");
    $this->addStyle("/src/dashboard/css/jquery.contextmenu.css");
    $this->addScript("/src/dashboard/js/Packery.js");
    $this->addScript("/src/dashboard/js/dashboard.js");
    $this->addStyle("/src/dashboard/css/dashboard.css");
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
