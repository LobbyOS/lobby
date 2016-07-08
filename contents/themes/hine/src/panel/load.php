<?php
use \Lobby\UI\Panel;
/**
 * Panel UI
 */
$panelLeftItems = Panel::getPanelItems("left");
$panelRightItems = Panel::getPanelItems("right");
?>
<nav>
  <ul class="left">
    <?php
    if(isset($panelLeftItems["lobbyAdmin"])){
      echo $this->makePanelTree("lobbyAdmin", $panelLeftItems["lobbyAdmin"]);
      unset($panelLeftItems["lobbyAdmin"]);
    }
    $html = "";
    foreach($panelLeftItems as $id => $item){
      if(count($item['subItems']) !== 0){
        $html .= $this->makePanelTree($id, $item);
      }else if($item['html'] != null){
        $html .= $this->makePanelItem($item['html'], "htmlContent", $id, "parent");
      }else{
        $html .= $this->makePanelItem($item['text'], $item['href'], $id, "parent");
      }
    }
    echo $html;
    ?>
  </ul>
  <ul class="right">
    <?php
    $html = "";
    foreach($panelRightItems as $id => $item){
      if(count($item['subItems']) !== 0){
        $html .= $this->makePanelTree($id, $item);
      }else if($item['html'] != null){
        $html .= $this->makePanelItem($item['html'], "htmlContent", $id, "parent");
      }else{
        $html .= $this->makePanelItem($item['text'], $item['href'], $id, "parent");
      }
    }
    echo $html;
    $this->addNotify();
    ?>
  </ul>
  <?php
  \Hooks::doAction("panel.end");
  ?>
</nav>
