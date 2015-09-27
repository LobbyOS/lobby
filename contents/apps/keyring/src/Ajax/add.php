<?php
if(isset($_POST['master']) && isset($_POST['key']) && isset($_POST['value'])){
  $master = htmlspecialchars($_POST['master']);
  $key = htmlspecialchars($_POST['key']);
  $value = htmlspecialchars($_POST['value']);
  
  if(!$this->MasterExists($master)){
    $add_form = $this->inc("/src/Inc/partial/create_master.php", array(
      "master_id" => strtolower($master),
      "master_name" => $master
    ));
    $response = array(
      'status' => 'master_does_not_exist'
    );
  }else if(!isset($_POST['password'])){
    $master_name = getData("master_" . $master . "_name");
    $add_form = $this->inc("/src/Inc/partial/add_key.php", array(
      "master_id" => strtolower($master),
      "master_name" => $master_name,
      "key" => $key
    ));
    $response = array(
      "status" => "type_password",
      "response" => '<div title="Create New Keyring"><center>'. $add_form .'</center></div>'
    );
  }else if($this->MasterLogin($master, $_POST['password'])){
    if($this->KeyAdd($master, $_POST['password'], $key, $value)){
      $response = array(
        "status" => "created"
      );
    }
  }else{
    $response = array(
      "status" => "password_wrong"
    );
  }
  echo json_encode($response);
}
