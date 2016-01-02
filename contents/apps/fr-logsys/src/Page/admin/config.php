<?php
require_once APP_DIR . "/src/Inc/partial/layout.php";
?>
<div class='contentLoader'>
  <h1>Settings</h1>
  <?php
  if(isset($_POST['db_host']) && isset($_POST['db_port']) && isset($_POST['db_name']) && isset($_POST['db_username']) && isset($_POST['db_password']) && isset($_POST['db_table'])){
    $config = array(
      "db_host" => $_POST['db_host'],
      "db_port" => $_POST['db_port'],
      "db_name" => $_POST['db_name'],
      "db_username" => $_POST['db_username'],
      "db_password" => $_POST['db_password'],
      "db_table" => $_POST['db_table']
    );
    $status = $this->connect($config);
    
    if($status === true){
      removeData("credentials");
      \H::saveJSONData("credentials", $config);
      sss("Connected", "I have successfully connected to database.");
    }else if($status == "no_table"){
      ser("No Table", "I couldn't find the table you mentioned in the database");
    }else{
      ser("Cound't Connect To Database", "I couldn't connect to the database with the credentials you gave. Please check it again.");
    }
  }
  $cfg = array_merge(array(
    "db_host" => "",
    "db_port" => "",
    "db_name" => "",
    "db_username" => "",
    "db_password" => "",
    "db_table" => ""
  ), is_array($this->dbinfo) ? $this->dbinfo : array());
  ?>
  <form action="<?php echo \Lobby::u();?>" method="POST">
    <label>
      <span>Database Host</span>
      <input type='text' name='db_host' value='<?php echo $cfg['db_host'];?>' />
    </label>
    <label>
      <span>Database Port</span>
      <input type='number' name='db_port' value='<?php echo $cfg['db_port'];?>' />
    </label>
    <label>
      <span>Username</span>
      <input type='text' name='db_username' value='<?php echo $cfg['db_username'];?>' />
    </label>
    <label>
      <span>Password</span>
      <input type='password' name='db_password' value='<?php echo $cfg['db_password'];?>' />
    </label>
    <label>
      <span>Database Name</span>
      <input type='text' name='db_name' value='<?php echo $cfg['db_name'];?>' />
    </label>
    <label>
      <span>Table</span>
      <input type='text' name='db_table' value='<?php echo $cfg['db_table'];?>' />
    </label>
    <button class="button red">Save</button>
  </form>
  <style>
  form label, form span{
    display: block;
  }
  form label{
    margin-bottom: 20px;
  }
  </style>
</div>
<?php require_once APP_DIR . "/src/Inc/partial/layout_footer.php";?>
