<div class="panel top">
  <div id="ajaxLoader" style="position: absolute;left:  0px;top: 0px;right: 0px;bottom: 0px;background: #6AAEEC;width: 0%;-webkit-transition: 1s;-moz-transition: 1s;"></div>
  <ul class="left">
    <?php
    \Lobby\Panel::panelItems("left");
    ?>
  </ul>
  <ul class="right">
    <?php \Lobby\Panel::panelItems("right"); ?>
  </ul>
</div>
