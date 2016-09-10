<?php
namespace Lobby\UI\Themes;

class hine extends \Lobby\UI\Theme {

  public function init(){
    $this->addScript("/src/main/lib/material-design/materialize.js");
    $this->addStyle("/src/main/lib/material-design/materialize.css");

    /**
     * jQuery UI
     */
    $this->addStyle("/src/main/lib/jquery-ui/jquery-ui.css");

    $this->addScript("/src/main/js/init.js");
    $this->addStyle("/src/main/css/font.css");
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
    $this->addScript("/src/dashboard/js/jquery.bxslider.js");
    $this->addStyle("/src/dashboard/css/jquery.bxslider.css");
    $this->addScript("/src/dashboard/js/jquery.contextmenu.js");
    $this->addStyle("/src/dashboard/css/jquery.contextmenu.css");
    $this->addScript("/src/dashboard/js/dashboard.js");
    $this->addStyle("/src/dashboard/css/dashboard.css");
  }

  public function makePanelTree($id, $item){
    $html = isset($item['html']) ? $item['html'] : substr($this->makePanelItem($item['text'], $item['href'], $id, "parent"), 0, -5);
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
      $html = "<li class='item $extraClass' id='home'><a href='". L_URL ."'></a></li>";
    }else if($href == "/admin"){
      /**
       * Admin button
       */
      $html = "<li class='item $extraClass' id='lobby'><a href='". \Lobby::u($href) ."' class='parent'>Lobby</a></li>";
    }else{
      $html = '<li class="item ' . $extraClass . '" id="' . $id . '">';
        if($href == ""){
          $html .= $text;
        }else if($href === "htmlContent"){
          $html .=  $text;
        }else{
          $html .= \Lobby::l($href, $text);
        }
      $html .= '</li>';
    }
    return $html;
  }

  /**
   * Adds the notify button and box
   */
  public function addNotify(){
    echo "<li class='item parent' id='notify'>";
      echo "<span title='Notifications' id='notifyToggle'></span>";
      echo "<div id='notifyBox'></div>";
    echo "</li>";
  }

}
