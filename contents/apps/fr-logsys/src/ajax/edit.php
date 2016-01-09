<?php
if(!isset($_POST['uid'])){
  ser("Invalid Request", "The request wasn't right.");
}else{
  $this->load();
  $sql = $this->dbh->prepare("SELECT * FROM `". $this->table ."` WHERE `id` = ?");
  $sql->execute(array($_POST['uid']));

  if($sql->rowCount() == 0){
    echo ser("User Not Found", "The user with the given ID doesn't exist.");
  }else{
    $id = $_POST['uid'];
    
    if(isset($_POST['update'])){
      /**
       * Update info except password
       */
      \fr_logsys\Fr\LS::updateUser($_POST['update'], $id);
      
      /**
       * Change Password
       */
      if(isset($_POST['user_password']) && $_POST['user_password'] != ""){
        \fr_logsys\Fr\LS::$user = $id;
        \fr_logsys\Fr\LS::$loggedIn = true;
        \fr_logsys\Fr\LS::changePassword($_POST['user_password']);
        \fr_logsys\Fr\LS::$user = null;
        \fr_logsys\Fr\LS::$loggedIn = false;
      }
      
      sss("Updated", "The user's data was successfully updated. <a href='javascript:window.location.reload();'>Reload page</a> to see changes.");
      
      $sql = $this->dbh->prepare("SELECT * FROM `". $this->table ."` WHERE `id` = ?");
      $sql->execute(array($id));
    }
    
    $info = $sql->fetch(\PDO::FETCH_ASSOC);
?>
    <h2><?php echo "Editing User '$id'";?></h2>
    <form id="updateUser">
      <input type='hidden' name='uid' value='<?php echo $id;?>' />
      <?php
      foreach($info as $column => $value){
        if($column != "id" && $column != "password" && $column != "password_salt"){
      ?>
          <label>
            <span><?php echo $column;?></span>
            <input type='text' name='update[<?php echo $column;?>]' value='<?php echo $value;?>' />
          </label>
      <?php
        }
        if($column == "password"){        
      ?>
          <label>
            <span>Password</span>
            <input type='password' name='user_password' placeholder='Leave empty to not change password' />
          </label>
      <?php
        }
      }
      ?>
      <button class='button green'>Update User</button>
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
      $("form#updateUser").die("submit").live("submit", function(){
        event.preventDefault();
        $("<a class='dialog'></a>").data({"params": $(this).serialize(), "dialog": "edit.php"}).appendTo(".workspace").click();
      });
    </script>
<?php
  }
}
?>
