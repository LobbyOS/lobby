<?php
if(isset($_POST['master']) && isset($_POST['key'])){
  $master_id = htmlspecialchars($_POST['master']);
  $key = htmlspecialchars($_POST['key']);
  
  if(!isset($_POST['password'])){
    $add_form = $this->inc("/src/inc/partial/get_key.php", array(
      "master_id" => strtolower($master_id),
      "key" => $key
    ));
    $response = array(
      "status" => "type_password",
      "response" => '<div title="Access KeyRing"><center>'. $add_form .'</center></div>'
    );
  }else{
    if($this->MasterLogin($master_id, $_POST['password'])){
      $value = $this->KeyGet($master_id, $_POST['password'], $key);
      $response = array(
        "status" => "done",
        "response" => $value
      );
    }else{
      $response = array(
        "status" => "password_wrong"
      );
    }
  }
  echo json_encode($response);
}
