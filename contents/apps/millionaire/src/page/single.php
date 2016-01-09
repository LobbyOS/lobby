<?php
$this->addScript("TimeCircles.js");
$this->addScript("jquery.letterfx.js");
$this->addScript("Fr.resources.js");
$this->addScript("single.js");

$this->addStyle("TimeCircles.css");
$this->addStyle("jquery.letterfx.css");
$this->addStyle("style.css");
$this->addStyle("mobile.css");

$questions = json_encode($this->questions());
$_SESSION['app-millionaire-level'] = 0;
?>
<div class="loading">
  <h1>Loading</h1>
  <div class='progress'><div class='inner'></div></div>
  <h2 class='status'></h2>
</div>
<script>
  (function(){
    lobby.app.currency = "<?php echo $this->currency;?>";
    lobby.app.level_money = <?php echo json_encode($this->money);?>;
    lobby.app.questions = <?php echo $questions;?>;
  })(lobby);
</script>
<div class="clearfix">
  <div class="left">
    <div class="overlay phoneafriend-container">
      <img src="<?php echo APP_SRC . "/src/image/phone.png";?>" />
      <div class="text"></div>
    </div>
    <div class="overlay audiencevote-container">
      <canvas style='width: 45em;height: 25em;'></canvas>
      <div class="results">
        <span>A</span><span>B</span><span>C</span><span>D</span>
      </div>
    </div>
    <div class="show_host">
      <div class="timer" data-timer="21" style="height:200px;width:200px;"></div>
      <div class="text"></div>
    </div>
    <div class="question"><div class='text'></div></div>
    <div class="options">
      <?php
      $letters = array(1 => "A", 2 => "B", 3 => "C", 4 => "D");
      for($i=1;$i < 5;$i++){
        echo "<div class='option'><span class='letter'>{$letters[$i]}.</span><div class='text'></div></div>";
      }
      ?>
    </div>
  </div>
  <div class="right">
    <div class="lifelines">
      <div class="lifeline" id="fifty-fifty"></div>
      <div class="lifeline" id="phoneafriend"></div>
      <div class="lifeline" id="audience-vote"></div>
    </div>
    <div class="questions">
      <?php
      for($i = 1;$i < 16;$i++){
        $money = $this->money[16 - $i];
        echo "<div class='question' data-money='{$money}'><span class='number'>". (16 - $i) ."</span><div class='money'>". $this->currency . $money ."</div></div>";
      }
      ?>
    </div>
  </div>
</div>
<div class="audios">
  
</div>
