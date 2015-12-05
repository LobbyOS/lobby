<?php
if(isset($master_id)){
?>
  <p>Please set a password for KeyRing "<?php echo $master_name;?>" that is going to be created.</p>
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
