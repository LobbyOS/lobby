<?php
$network = \H::input("network");
$this->addScript("connect.js");
if(isset($this->available_networks[$network])){
  $Network = ucfirst($network);
?>
  <div class='contents'>
    <h1>Connect <?php echo $Network;?></h1>
    <p>Please enter your <?php echo ucfirst($network);?> account's username and password.</p>
    <form id='login_form' data-network="<?php echo $network;?>">
      <input type='text' name='username' placeholder='Email/Phone Number' />
      <input type='password' name='password' placeholder='Password' />
      <input type='submit' value='Log In To <?php echo $Network;?>' />
    </form>
    <div id='login_status' clear></div>
  </div>
  <style>
    .contents input{
      display: block;
      margin-top: 5px;
    }
  </style>
<?php
}else{
  ser();
}
