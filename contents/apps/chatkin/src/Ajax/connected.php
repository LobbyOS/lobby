<?php
if(isset($_POST['network']) && isset($_POST['id']) && isset($this->available_networks[$_POST['network']])){
  H::saveJSONData("accounts", array(
    $_POST['network'] => array($_POST['id'] => array(false))
  ));
  var_dump(H::getJSONData("accounts"));
}
