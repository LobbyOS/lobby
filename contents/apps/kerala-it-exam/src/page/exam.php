<?php
if(!isset($_SESSION['kerala-it-exam-rid'])){
  \Lobby::redirect("/app/kerala-it-exam");
}

/**
 * If questions are not set, make questions
 */
if(!isset($_SESSION['kerala-it-exam-qs'])){
  $this->generateQuestions($_SESSION['kerala-it-exam-class']);
}

$this->addStyle("exam.css");
$this->addScript("jquery.countdown.js");
$this->addScript("exam.js");
$questions = $_SESSION['kerala-it-exam-qs'];
?>
<div class="top">
  <div class="right">
    <div id="time"></div>
    <span>
      <a class='button red' id='finishTheory'>Finish Theory Examination</a>
    </span>
  </div>
  <div>
    Register No : <span><?php echo $_SESSION['kerala-it-exam-rid'];?></span>
    <span>
      <?php echo $this->l("/restart", "Restart Exam");?>
    </span>
  </div>
</div>
<form class="exam">
  <ul class="tabs examTabs">
    <li class='tab' class="active"><a href="#short-box">Short Answer</a></li>
    <li class='tab'><a href="#multiple-box">Multiple Choice</a></li>
    <li class='tab'><a href="#note-box">Short Note</a></li>
  </ul>
  <div id="short-box" style="display: table;height: 85%;">
    <div class="left">
      <?php
      $this->sidebar("short");
      ?>
    </div>
    <div class="right">
      <?php
      $this->outputQuestions($questions, "short");
      ?>
    </div>
  </div>
  <div id="multiple-box">
    <div class="left">
      <?php
      $this->sidebar("multiple");
      ?>
    </div>
    <div class="right">
      <?php
      $this->outputQuestions($questions, "multiple");
      ?>
    </div>
  </div>
  <div id="note-box">
    <div class="left">
      <?php
      $this->sidebar("note");
      ?>
    </div>
    <div class="right">
      <?php
      $this->outputQuestions($questions, "note");
      ?>
    </div>
  </div>
  <div id="practical-box">
    <ul class="tabs groups">
      <li class='tab' class="active" data-id="0"><a>Group 1</a></li>
      <li class='tab' data-id="1"><a>Group 2</a></li>
      <li class='tab' data-id="2"><a>Group 3</a></li>
      <li class='tab' data-id="3"><a>Group 4</a></li>
    </ul>
    <ul class="choices">
      <li data-id="0"><a class="button active">Choice 1</a></li>
      <li data-id="1"><a class="button">Choice 2</a></li>
    </ul>
    <div class='questionArea'>
      <div class="question"></div>
    </div>
  </div>
</form>
<div id="expired_overlay">
  <h1>Time Over</h1>
  <p>Sorry, the allotted time of 1 hour has ended.</p>
  <p>
    <?php echo \Lobby::l("/app/kerala-it-exam/restart", "Restart Exam", "class='button red'");?>
  </p>
</div>
<div id="finished_overlay">
  <h1>Finished</h1>
  <p>Congratulations, you have successfully finished the examination.</p>
  <p>Your Score is :</p>
  <h2></h2>
  <p>
    <?php echo \Lobby::l("/app/kerala-it-exam/restart", "Restart Exam", "class='button red'");?>
  </p>
  <div id="result_analysis"></div>
</div>
<script>
  <?php
  echo "var practicalQuestions = ". json_encode($questions['practical']) . ";";
  ?>
  lobby.load(function(){
    lobby.app.practicalQuestions = practicalQuestions;
    lobby.app.startExam();
  });
</script>
