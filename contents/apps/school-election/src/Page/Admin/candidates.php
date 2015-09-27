<?php
include APP_DIR . "/src/Inc/load.php";

if(isset($_POST['update_male_candidates']) || isset($_POST['update_female_candidates'])){
  $candidates = isset($_POST['update_male_candidates']) ? getData("male_candidates") : getData("female_candidates");
  $candidates = unserialize($candidates);
  
  foreach($_POST['candidates'] as $id => $value){
    $candidates[$id] = $value;
  }
  
  if(isset($_POST['update_male_candidates'])){
    saveData("male_candidates", serialize($candidates));
  }else{
    saveData("female_candidates", serialize($candidates));
  }
}

if( isset($_POST['add_male_candidates']) || isset($_POST['add_female_candidates']) ){
  $data = array();
  $i = isset($_POST['add_male_candidates']) ? 0 : 50;
  
  foreach($_POST['candidates'] as $candidate_name){
    $data[$i] = $candidate_name;
    $i++;
  }
  if(isset($_POST['add_female_candidates'])){
    // Girls
    saveData("female_candidates", serialize($data));
  }else{
    // Boys
    saveData("male_candidates", serialize($data));
  }
}
?>
<div class="content-full">
  <h2>Boys</h2>
  <p>Use the below form to change details of the <b>Boys</b> Candidates</p>
  <?php
  if($ELEC->isElection("male")){
    $candidates = unserialize(getData("male_candidates"));
    echo "<form method='POST'>";
      foreach($candidates as $id => $candidate){
        echo "<div class='item'>";
          echo "<input type='text' size='30' name='candidates[{$id}]' value='{$candidate}' />";
        echo "</div>";
      }
      echo "<div class='item'>";
        echo "<button name='update_male_candidates'>Update Boys' Details</button>";
      echo "</div>";
    echo "</form>";
  }else{
  ?>
    <form method="POST">
      <?php
      for($i = 0; $i < $this->config['male_candidates']; $i++){
        echo "<div class='item'>";
          echo "<input name='candidates[]' placeholder='Candidate # ". ($i + 1) ."' />";
        echo "</div>";
      }
      echo "<div class='item'>";
        echo "<button name='add_male_candidates'>Add Boys Candidates</button>";
      echo "</div>";
      ?>
    </form>
  <?php
  }
  ?>
  <h2>Girls</h2>
  <p>Use the below form to change details of the <b>Girls</b> Candidates</p>
  <?php
  if($ELEC->isElection("female")){
    $candidates = unserialize(getData("female_candidates"));
    
    echo "<form method='POST'>";
      foreach($candidates as $id => $candidate){
        echo "<div class='item'>";
          echo "<input type='text' size='30' name='candidates[{$id}]' value='{$candidate}' />";
        echo "</div>";
      }
      echo "<div class='item'>";
        echo "<button name='update_female_candidates'>Update Girls' Details</button>";
      echo "</div>";
    echo "</form>";
  }else{
  ?>
    <form method="POST">
      <?php
      for($i = 0; $i < $this->config['female_candidates']; $i++){
        echo "<div class='item'>";
          echo "<input name='candidates[]' placeholder='Candidate # ". ($i + 1) ."' />";
        echo "</div>";
      }
      echo "<div class='item'>";
        echo "<button name='add_female_candidates'>Add Girls Candidates</button>";
      echo "</div>";
      ?>
    </form>
  <?php
  }
  ?>
</div>
<style>
.workspace .item{
  margin-top:10px;
}
</style>
