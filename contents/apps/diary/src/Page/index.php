<?php
if(isset($_POST['diary']) && H::csrf()){
  // Set name
  saveData("name", $_POST['diary']);
}
?>
<div class="contents">
  <h1>Diary</h1>
  <p style="font-style: italic;margin-bottom: 20px;">Saving memories of your life</p>
  <p>Choose a date to view it's entry or <?php echo $this->l("/entry", "write for today", true);?></p>
  <div class="datepicker" style="margin: 10px auto;"></div>
  <form action="<?php echo \Lobby::u();?>" method="POST">
    <p>Want to name your diary ?</p>
    <?php H::csrf(1);?>
    <input type="text" name="diary" placeholder="Type name here... (Kitty, John)" value="<?php echo getData("name");?>" />
    <button class="button red">Submit</button>
  </form>
  <?php
  if(isset($_POST['diary'])){
    sss("Name Set", "Your diary's name has been set.");
  }
  ?>
  <script>
  $(window).load(function(){
    lobby.app.written_dates = <?php echo getData("written_dates") ?: '[]';?>;
    availableDates = lobby.app.written_dates;
    availableDates.push($.datepicker.formatDate('dd-mm-yy', new Date()));
    
    function available(date) {
      dmy = $.datepicker.formatDate($(".datepicker").datepicker( "option", "dateFormat" ), date);

      if ($.inArray(dmy, availableDates) != -1) {
        return dmy == $.datepicker.formatDate('dd-mm-yy', new Date()) ? [true, "", "Write Today's Diary"] : [true, "", "Click to see diary entry"];
      } else {
        return [false, "", "No Entry in this date"];
      }
    }

    $(".datepicker").datepicker({
      autoSize: true,
      dateFormat: "dd-mm-yy",
      maxDate: "+0d",
      showWeek: true,
      changeMonth: true,
      changeYear: true,
      weekHeader: "Week",
      dayNamesMin: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
      
      beforeShowDay: available,
      onSelect: function(date){
        if($.datepicker.formatDate($(".datepicker").datepicker("option", "dateFormat"), new Date()) == date){
          lobby.app.redirect("/entry");
        }else{
          lobby.app.redirect("/entry/"+date);
        }
      }
    });
  });
  </script>
  <style>
    .ui-datepicker {
      width:auto;
      font-size: 20px;
    }
    .datepicker tbody tr{
      background: none;
    }
    .datepicker tr td{
      box-shadow: none;
      -webkit-box-shadow: none;
    }
    .datepicker tr td:nth-child(1){
      border: none;
      text-align: center;
    }
  </style>
</div>
