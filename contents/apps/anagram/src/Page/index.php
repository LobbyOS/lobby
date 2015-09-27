<?php
\Lobby::addScript("dynscroll", "/includes/lib/scrollbar/scrollbar.js");
\Lobby::addStyle("dynscroll", "/includes/lib/scrollbar/scrollbar.css");

$this->addScript("/jquery.scrollTo.js");
$this->addStyle("/game.css");
$this->addScript("/game.js");
?>
<div class="contents">
  <div class="loading">
    <img src="<?php echo APP_SRC;?>/src/Image/loading.gif" />
  </div>
  <div class="controls">
    <a id="newGame">New</a>
    <a id="solveGame">Solve</a>
  </div>
  <div class="letters"></div>
  <div class="input"></div>
  <div class="submit">
    <a class='button'>Submit</a>
    <div class='status'></div>
  </div>
  <div class="boxes">
    <div class="boxes-inner">
      <div class='section' id='sec1'></div>
    </div>
  </div>
  <audio src='<?php echo APP_SRC;?>/src/Audio/wrong.mp3' id='wrong'></audio>
  <audio src='<?php echo APP_SRC;?>/src/Audio/correct.mp3' id='correct'></audio>
  <audio src='<?php echo APP_SRC;?>/src/Audio/select.mp3' id='select'></audio>
</div>
