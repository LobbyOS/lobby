<?php
include APP_DIR . "/src/inc/load.php";
?>

<div class="content-full" style="top:-40%;width:600px;">
	<h2>Statistics</h2>
	<p>Find the people who didn't vote</p>
	<form method="POST">
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
    <h2>Class Strength</h2>
    <input name="roll" type="number" placeholder="Roll Number" autocomplete="off" />
    <button style="margin-top: 15px;">Find</button>
	</form>
	<?php
	if(isset($_POST['class']) && isset($_POST['division']) && isset($_POST['roll'])){
    if(is_numeric($_POST['roll']) && $_POST['roll'] <= $this->config['max_strength'] ){
	    echo "<p>People who haven't voted in Class <strong>{$_POST['class']}</strong> of division <strong>{$_POST['division']}</strong> :</p>";
	    
      $absent = 0;
	    echo "<ol>";
        for($i=1;$i < (int) $_POST['roll'] + 1;$i ++){
	        $id	= strtoupper($_POST['class'] . $_POST['division'] . $i);
	
	        if($ELEC->didVote($id) == false){
            echo "<li>$id</li>";
            $absent++;
	        }
        }
	    echo "</ol>";
      echo "<table><tbody>";
        echo "<tr>";
	        echo "<td>Total students</td>";
          echo "<td>{$_POST['roll']}</td>";
        echo "</tr>";
        echo "<tr>";
	        echo "<td>Total students that have voted</td>";
          echo "<td>". ($_POST['roll'] - $absent) ."</td>";
        echo "</tr>";
        echo "<tr>";
	        echo "<td>Total students that didn't vote</td>";
          echo "<td>$absent</td>";
        echo "</tr>";
      echo "</tbdoy></table>";
    }
	}
	?>
</div>
