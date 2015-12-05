<div class='contents'>
  <h1>KeyRing</h1>
  <p>Store your sensitive informations securely</p>
  <h2>Create New KeyRing</h2>
  <?php
  if(isset($_POST['keyring_id']) && isset($_POST['keyring_name']) && isset($_POST['keyring_password']) && isset($_POST['keyring_retyped_password'])){
    $id = strtolower($_POST['keyring_id']);
    $name = $_POST['keyring_name'];
    $pass = $_POST['keyring_password'];
    $desc = $_POST['keyring_description'];
    
    if(!ctype_alpha($id)){
      ser("Invalid ID", "Keyring ID should onlt contain alphabets");
    }elseif(strlen($pass) < 6){
      ser("Invalid Password", "A password should have minimum characters of 6. Your's doesn't even have 6 characters.");
    }elseif($pass != $_POST['keyring_retyped_password']){
      ser("Passwords Mismatch", "The passwords you entered didn't match. Please try again.");
    }else{
      if($this->MasterAdd($id, $name, $desc, $pass)){
        sss("Created KeyRing", "Your keyring was successfulyl created.");
      }else{
        ser("KeyRing Exists", "The keyring with the ID you gave already exists");
      }
    }
  }
  ?>
  <form method='POST' action='<?php echo APP_URL;?>/new-master'>
    <label>
      <span>ID</span>
      <input type='text' name='keyring_id' placeholder='A unique KeyRing ID. Lowercase ALPHABETS Only' />
    </label>
    <label>
      <span>Name</span>
      <input type='text' name='keyring_name' placeholder='KeyRing Name. Whitespaces allowed' />
    </label>
    <label>
      <span>Password</span>
      <input type='password' name='keyring_password' placeholder='Minimum value of 6 characters' />
    </label>
    <label>
      <span>Retype Password</span>
      <input type='password' name='keyring_retyped_password' />
    </label>
    <label>
      <span>Description</span>
      <input type='password' name='keyring_description' placeholder='Anything to say about this keyring ?' />
    </label>
    <button clear>Create KeyRing</button>
  </form>
  <style>
    .contents label span{
      display: block;
    }
    .contents label input{
      width: 400px;
    }
  </style>
</div>
