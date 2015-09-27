<?php
unset($_SESSION['kerala-it-exam-rid']);
unset($_SESSION['kerala-it-exam-qs']);
unset($_SESSION['kerala-it-exam-class']);
?>
<div class="contents">
  <h1>Please Wait....</h1>
  <p>I'm doing some ninja stuff. So, please wait for 2 seconds. :-)</p>
  <script>
  localStorage["end_time"] = "invalid"; // In case `delete` didn't work
  delete localStorage["end_time"];
  setTimeout(function(){
    window.location = "<?php echo \Lobby::u("/app/kerala-it-exam");?>";
  }, 50);
  </script>
</div>
