<?php
/**
 * Render all panels
 */

use \Lobby\UI\Panel;

$topPanelLeftItems = Panel::getTopItems("left");
$topPanelRightItems = Panel::getTopItems("right");

$leftPanelTopItems = Panel::getLeftItems("top");
$leftPanelBottomItems = Panel::getLeftItems("bottom");
?>
<nav id="panel-top">
  <?php
  \Hooks::doAction("panel.top.begin");

  if(!empty($leftPanelTopItems) || !empty($leftPanelBottomItems)){
  ?>
    <a href="#" data-activates="panel-left" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
  <?php
  }
  ?>
  <ul id="panel-top-left">
    <?php
    if(isset($topPanelLeftItems["lobbyAdmin"])){
      echo $this->makePanelTree("lobbyAdmin", $topPanelLeftItems["lobbyAdmin"]);
      unset($topPanelLeftItems["lobbyAdmin"]);
    }
    $html = "";
    foreach($topPanelLeftItems as $id => $item){
      $class = "parent " . $item["class"];

      if(count($item['subItems']) !== 0){
        $html .= $this->makePanelTree($id, $item);
      }else if($item['html'] != null){
        $html .= $this->makePanelItem($item['html'], "htmlContent", $id, $class);
      }else{
        $html .= $this->makePanelItem($item['text'], $item['href'], $id, $class);
      }
    }
    echo $html;
    ?>
  </ul>
  <ul id="panel-top-right">
    <?php
    $html = "";
    foreach($topPanelRightItems as $id => $item){
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
  \Hooks::doAction("panel.top.end");
  ?>
</nav>
<?php
/**
 * Left Panel aka sidebar
 */
if(!empty($leftPanelTopItems) || !empty($leftPanelBottomItems)){
?>
  <div class="side-nav fixed" id="panel-left">
    <ul id="panel-left-top">
      <?php
      $html = "";
      foreach($leftPanelTopItems as $id => $item){
        $class = "parent " . $item["class"];

        if(count($item['subItems']) !== 0){
          $html .= $this->makePanelTree($id, $item);
        }else if($item['html'] != null){
          $html .= $this->makePanelItem($item['html'], "htmlContent", $id, $class);
        }else{
          $html .= $this->makePanelItem($item['text'], $item['href'], $id, $class);
        }
      }
      echo $html;
      ?>
    </ul>
  </div>
<?php
}
?>