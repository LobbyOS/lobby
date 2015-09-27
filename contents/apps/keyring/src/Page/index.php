<div class='contents'>
  <h1>KeyRing</h1>
  <p>Store your sensitive informations securely.</p>
  <?php
  if($this->set){
    echo "<div style='margin-left: 20px;'>";
    foreach(\H::getJSONData("keyrings") as $master => $null){
      $name = getData("master_$master" . "_name");
      echo $this->l("/view?id=$master", $name, "class='button red'") . "<cl/>";
    }
    echo "</div>";
  }else{
    sme("No KeyRings", "You haven't created any keyrings");
  }
  ?>
    <a href='<?php echo APP_URL;?>/new-master' class='button green'>Create A New KeyRing</a>
</div>
