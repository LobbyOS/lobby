<?php
include APP_DIR . "/src/inc/load.php";
$this->setTitle("Admin Panel");
?>
<div class="content-full">
  <h2>Admin Panel</h2>
  <?php
  if( isset($_GET['action']) ){
    $ELEC->liveChange($_GET['action']);
    sss("Requested For {$_GET['action']}", "The request to reload election page has been sent.");
  }
  if( isset($_POST['clearData']) ){
    $ELEC->clear();
    echo "<h2>Successfully Cleared Data</h1>";
  }
  ?>
  <div>
    <?php
    echo \Lobby::l("/admin/app/school-election/candidates", "Candidates Enrollment", "class='button'");
    ?>
  </div>
  <div style="margin-top:10px;">
    <?php
    echo \Lobby::l("/admin/app/school-election/create-users", "Generate Election Passwords", "class='button'");
    ?>
  </div>
  <div style="margin-top:10px;">
    <?php
    echo \Lobby::l("/admin/app/school-election/stats", "Election Statistics", "class='button green'");
    ?>
  </div>
  <div style="margin-top:10px;">
    <?php
    echo \Lobby::l("/admin/app/school-election/didnt-vote", "Who Didn't Vote ?", "class='button'");
    ?>
  </div>
  <h2>Other Tools</h2>
  <div style="margin-top:10px;">
    <form action="<?php echo \Lobby::u();?>" method="POST" onsubmit="return confirm('Are you sure ?') !== true ? false : true;"><button title="Empty all the data of election stored in database ?" name="clearData" class="button red">CLEAR ALL DATA !</button></form>
  </div>
  <div style="margin-top:10px;">
    <a href="?action=reload" class="button">Reload Election Pages</a>
  </div>
  <div style="margin-top:10px;">
    <a href="?action=reset" class="button">Reset Live Actions</a>
  </div>
</div>
