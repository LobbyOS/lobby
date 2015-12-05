<?php
$this->load();

if(isset($_POST['new'])){
  $omitted_array = $_POST['new'];
  unset($omitted_array['password']);
  unset($omitted_array['username']);
    
  \fr_logsys\Fr\LS::register($_POST['new']['username'], $_POST['new']['password'], $omitted_array);
  sss("Created", "The user has been created. <a href='javascript:window.location.reload();'>Reload page</a> to see changes.");
}

$columns = $this->dbh->query("DESCRIBE `". $this->table ."`")->fetchAll();
?>
<h2>New User</h2>
<form id="newUser">
  <?php
  foreach($columns as $column_info){
    $column = $column_info['Field'];
    if($column != "id" && $column != "password_salt"){
  ?>
      <label>
        <span><?php echo ucfirst($column);?></span>
        <input type='text' name='new[<?php echo $column;?>]' />
      </label>
  <?php
    }
  }
  ?>
  <button class='green'>Create User</button>
</form>
<style>
  form label{
    display: block;
    margin-bottom: 10px;
  }
  form label span{
    display: block;
    margin-top: 2px;
  }
</style>
<script>
  $("form#newUser").die("submit").live("submit", function(){
    event.preventDefault();
    $("<a class='dialog'></a>").data({"params": $(this).serialize(), "dialog": "new_user.php"}).appendTo(".workspace").click();
  });
</script>
