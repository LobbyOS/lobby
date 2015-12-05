<?php
if(isset($key)){
?>
  <p>Please type the password for KeyRing "<?php echo $master_id;?>" to access value of key "<?php echo $key;?>".</p>
  <input type='password' onload='$(this).focus()' />
<?php
}
