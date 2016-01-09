<?php
require_once APP_DIR . "/src/inc/partial/layout.php";
?>
<div class='contentLoader'>
  <h1>Users</h1>
  <?php
  if($this->set){
    $this->load();
    
    echo "<a class='button dialog' data-dialog='new_user.php'>New User</a>";
    echo "<a class='button green dialog' data-dialog='new_col.php'>Add New Column</a>";
    echo "<a class='button green dialog' data-dialog='export.php'>Export as SQL</a>";
    
    if(isset($_POST['remove_user'])){
      $sql = $this->dbh->prepare("DELETE FROM `". $this->table ."` WHERE `id` = ?");
      $sql->execute(array($_POST['remove_user']));
      sss("Removed User", "The user with the ID '". htmlspecialchars($_POST['remove_user']) ."' was deleted from the database");
    }
    
    $_GET['start'] = isset($_GET['start']) ? $_GET['start'] : 0;
    
    $sql = $this->dbh->prepare("SELECT * FROM `". $this->table ."` ORDER BY `id` LIMIT :start, 10");
    $sql->bindValue(":start", (int) trim($_GET['start']), \PDO::PARAM_INT);
    $sql->execute();
    
    $results = $sql->fetchAll(\PDO::FETCH_ASSOC);
    $usersCount = $sql->rowCount();
    
    if($usersCount == 0){
      echo sme("No Users", "There are currently no users stored in the table.");
    }else{
      echo "<table><thead>";
        echo "<th width='15%'>Actions</th>";
        $description = array(
          "User ID" => "uid: The user's unique ID",
          "username" => "username: Username of user",
          "created" => "created: The date when the user created her/his account",
          "attempt" => "attempt: The number of times the user have attempted logins or the time for which the user was blocked from loggging in."
        );
        
        $sql = $this->dbh->query("DESCRIBE `". $this->table ."`");
        foreach($sql->fetchAll() as $null => $column){
          $column_name = $column['Field'];
          if($column_name != "password" && $column_name != "password_salt"){
            $column_name = $column_name == "id" ? "User ID" : $column_name;
            echo "<th title='". (isset($description[$column_name]) ? $description[$column_name] : $column_name) ."'>". ucfirst($column_name) ."<a class='removeColumn' title='Delete Column' data-column='$column_name'></a></th>";
          }
        }
      echo "</thead><tbody>";
      foreach($results as $r){
        $id = $r['id'];
  ?>
        <tr>
          <td><?php
          echo "<a class='button dialog' data-dialog='edit.php' data-params=\"uid=$id\">Edit</a>";
          echo "<form id='clear_form' action='". APP_URL ."/admin/users' method='POST' style='display: inline-block;'><input type='hidden' name='remove_user' value='$id'/><a class='button red' onclick=\"confirm('Are you sure you want to delete the user ?') ? $(this).parents('form').submit() : '';\">Remove</a></form>";
          ?></td>
          <td><?php echo $id;?></td>
          <td><?php echo $r['username'];?></td>
          <?php
          foreach($r as $column_name => $column_value){
            if($column_name != "id" && $column_name != "username" && $column_name != "password" && $column_name != "password_salt"){
              echo "<td>". $column_value ."</td>";
            }
          }
          ?>
        </tr>
  <?php
      }
      echo "</tbody></table>";
      echo "<center>";
        for($i=1;$i < ceil($usersCount / 10) + 1;$i++){
          $start = ($i - 1) * 10;
          echo "<a href='". ($i != 1 ? APP_URL . "/admin/users?start=$start" : APP_URL . "/admin/users") ."' class='button ". (isset($_GET['start']) && $_GET['start'] == $start ? "green" : "") ."'>$i</a>";
        }
      echo "</center>";
    }
  }else{
  ?>
    <a href='<?php echo APP_URL;?>/admin/config' class='button red'>Setup logSys Admin</a>
  <?php
  }
  ?>
</div>
<?php require_once APP_DIR . "/src/inc/partial/layout_footer.php";?>

