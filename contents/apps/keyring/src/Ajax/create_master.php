<?php
if(isset($_POST['master_id']) && isset($_POST['master_name']) && isset($_POST['master_description'])){
  $master_id = htmlspecialchars($_POST['master_id']);
  $master_name = htmlspecialchars($_POST['master_name']);
  $master_description = htmlspecialchars($_POST['master_description']);
  
  if(!isset($_POST['master_password'])){
    $add_form = $this->inc("/src/inc/partial/create_master.php", array(
      "master_id" => strtolower($master_id),
      "master_name" => $master_name
    ));
    $response = array(
      "status" => "type_password",
      "response" => '<div title="Create New Keyring"><center>'. $add_form .'</center></div>'
    );
  }else{
    if($this->MasterAdd($master_id, $master_name, $master_description, $_POST['master_password'])){
      $response = array(
        "status" => "created"
      );
    }
  }
  echo json_encode($response);
}
