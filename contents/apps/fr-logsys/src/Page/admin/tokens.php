<?php
require_once APP_DIR . "/src/Inc/partial/layout.php";
?>
<div class='contentLoader'>
  <h1>Tokens</h1>
  <p>Tokens are used while user forgets password or on 2 step verification.</p>
  <?php
  if($this->set){
    $this->load();
    
    if(isset($_POST['clear_tokens'])){
      $sql = \fr_logsys\Fr\LS::$dbh->prepare("TRUNCATE TABLE `resetTokens`");
      $sql->execute();
      echo sme("Tokens Cleared", "All tokens were cleared from the table");
    }
    
    $_GET['start'] = isset($_GET['start']) ? $_GET['start'] : 0;
    
    $sql = \fr_logsys\Fr\LS::$dbh->prepare("SELECT * FROM `resetTokens` LIMIT :start, 10");
    $sql->bindParam(":start", $_GET['start'], \PDO::PARAM_INT);
    $sql->execute();
    
    if($sql->rowCount() == 0){
      echo sme("No Tokens", "There are currently no tokens stored in the table.");
    }else{
      echo "<table><thead><th width='30%'>User</th><th width='50%'>Token</th><th title='YYYY-MM-DD HH:MM:SS' width='20%'>Created</th></thead><tbody>";
      while($r = $sql->fetch()){
  ?>
        <tr>
          <td title="User ID: <?php echo $r['uid'];?>"><?php echo \fr_logsys\Fr\LS::getUser("name", $r['uid']);?></td>
          <td><?php echo $r['token'];?></td>
          <td><?php echo $r['requested'];?></td>
        </tr>
  <?php
      }
      echo "</tbody></table>";
      echo "<form id='clear_form' action='". APP_URL ."/admin/tokens' method='POST'><input type='hidden' name='clear_tokens'/><a class='button red' onclick=\"confirm('Are you sure you want to delete all tokens') ? $('.workspace #clear_form').submit() : '';\">Clear Tokens</a></form>";
    }
  }else{
  ?>
    <a href='<?php echo APP_URL;?>/admin/config' class='button red'>Setup logSys Admin</a>
  <?php
  }
  ?>
</div>
<?php require_once APP_DIR . "/src/Inc/partial/layout_footer.php";?>

