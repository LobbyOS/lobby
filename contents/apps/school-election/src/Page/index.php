<?php
include APP_DIR . "/src/Inc/load.php";

$this->addStyle("style.css");
$this->addScript("election.js");
$_SESSION['election-validated'] = "false";
?>
<script>
(function(){
  lobby.app.config = <?php echo json_encode($this->config);?>;
})(lobby);
</script>
<div class="content">
  <form action="info.php" id="voterForm">
    <h2>Class</h2>
    <select name="class">
      <?php
      foreach($this->config['classes'] as $class){
        echo "<option name='$class'>$class</option>";
      }
      ?>
    </select>
    <h2>Division</h2>
    <select name="division">
      <?php
      $divs = $this->config['divisions'];
      foreach($divs as $div){
        echo "<option name='$div'>$div</option>";
      }
      ?>
    </select>
    <h2>Roll Number</h2>
    <input name="roll" type="number" placeholder="Roll Number" autocomplete="off" />
      <h2>Password</h2>
    <input name="password" type="password" placeholder="Password" autocomplete="off" />
    <div style="margin-top: 20px;">
      <button name="submit">Login To Vote</button>
    </div>
  </form>
  <form action="vote.php" id="voteForm">
    <div class="candidates">
       <?php $ELEC->showCandidates();?>
    </div>
    <button class="vote" name="vote" value="vote">Cast Your Vote</button>
    <div id="username"></div>
  </form>
  <div class='thankyou'>
    <h1>Thank You</h1>
    <p>Your vote was entered successfully.</p>
  </div>
</div>
<div class="quote">
  Democracy is tolerance. It is the tolerance not only towards
  <div style="margin-left:90px;">
    those who agree with us, but also towards those who disagree.
  </div>
  <p style="text-align:right;display:inline-block;">
    - Jawarhalal Nehru
  </p>
</div>
<style>
body{  
  -webkit-user-select: none;
  -moz-user-select: -moz-none;
  -ms-user-select: none;
  user-select: none;
}
</style>
