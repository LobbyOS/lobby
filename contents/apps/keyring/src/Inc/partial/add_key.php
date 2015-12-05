<?php
if(isset($key)){
?>
  <p>Please type the password for KeyRing "<?php echo $master_name;?>" to create key "<?php echo $key;?>".</p>
  <input type='password' onload='$(this).focus()' />
<?php
}else{
?>
  <form method="POST">
    <label>
      <span>KeyRing Name</span>
      <input type='text' name='' />
    </label>
  </form>
<?php
}
?>
