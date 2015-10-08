<?php
/**
 * Panel UI
 */
$panelLeftItems = \Lobby\UI\Panel::getPanelItems("left");
?>
<div class="panel top">
  <ul class="left">
    <?php echo $this->makePanelTree("lobbyAdmin", $panelLeftItems["lobbyAdmin"]);unset($panelLeftItems["lobbyAdmin"]);?>
    <?php 
    $html = "";
    foreach($panelLeftItems as $id => $item){
      if( !isset($item['subItems']) ){
        if( !isset($item['text']) && isset($item['html']) ){
          $html .= $this->makePanelItem($item['html'], "htmlContent", $id, "prnt");
        }else{
          $html .= $this->makePanelItem($item['text'], $item['href'], $id, "prnt");
        }
     }else{
        $html .= $this->makePanelTree($id, $item);
      }
    }
    echo $html;
    ?>
  </ul>
  <ul class="right">
    <?php \Lobby\UI\Panel::getPanelItems("right"); ?>
  </ul>
</div>
