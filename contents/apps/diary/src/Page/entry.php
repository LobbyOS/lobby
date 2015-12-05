<?php
$this->addStyle("font-awesome.css");
$this->addStyle("jquery.notebook.css");
$this->addStyle("diary.css");

$today = false;
if(!isset($entry_date)){
  $entry_date = date("d-m-Y");
  $today = true;
}

$entry = getData($entry_date);
if($entry == null && !$today){
  ser("No Entry", "No diary entry was found on the date " . htmlspecialchars($entry_date));
}else{
  $entry = preg_replace("/\[p\](.*?)\[\/p\]/", "<p>$1</p>", $entry);
  $entry = str_replace("<p> </p>", "<br/>", $entry);
?>
  <div class="contents">
    <h1>Diary</h1>
    <p style="font-style: italic;margin-bottom: 20px;">Saving memories of your life</p>
    <?php
    if($today){
      $this->addScript("jquery.notebook.js");
      $this->addScript("diary.js");
      echo '<p>Diary will be saved in <span id="seconds_counter">30</span> seconds, <a class="button green" id="save_diary">Save Now</a></p>';
    }
    ?>
    <div class="diary">
      <div class="paper" style="min-height: 100px;">
        <div class="date"><?php echo date("F j, Y", strtotime($entry_date)) . "<br/>" . date("l", strtotime($entry_date));?></div><br/>
        <div class="dear">Dear <?php echo htmlspecialchars(getData("name")) ?: "diary";?>,</div>
        <div class="entry"><?php echo $entry;?></div>
      </div>
    </div>
  </div>
  <script>
    $(function(){
      lobby.app.date = "<?php echo $entry_date;?>";
    });
  </script>
<?php
}
?>
