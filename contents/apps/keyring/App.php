<?php
class keyring extends \Lobby\App {
  
  public $set = false;
  public $master_salt = "Lobby_Key_Ring";
  
  public function page(){
    if(count(\H::getJSONData("keyrings")) != 0){
      $this->set = true;
    }
    return "auto";
  }
  
  public function MasterAdd($id, $name, $description, $password){
    $random_salt = \Lobby::randStr(15);
    $hashed = hash("sha512", $this->master_salt . $password . $random_salt);
    
    if(!$this->MasterExists($id)){
      saveData("master_". $id ."_name", $name);
      saveData("master_". $id ."_description", $description);
      saveData("master_". $id ."_password", $hashed);
      saveData("master_". $id ."_password_salt", $random_salt);
      saveData("master_". $id ."_items", '');
      
      \H::saveJSONData("keyrings", array($id => 1));
      
      return true;
    }else{
      return false;
    }
  }
  
  public function MasterExists($id){
    if(getData("master_". $id ."_name") == null){
      return false;
    }else{
      return true;
    }
  }
  
  public function MasterLogin($master, $password){
    $salt = getData("master_". $master . "_password_salt");
    $hashed = getData("master_". $master . "_password");
    
    if(hash("sha512", $this->master_salt . $password . $salt) == $hashed){
      return true;
    }else{
      return false;
    }
  }
  
  public function KeyAdd($master, $password, $key, $value){
    if($this->MasterExists($master)){
      require_once APP_DIR . "/src/Inc/Crypto.php";
      
      $items = getData("master_". $master ."_items");
      $pass_salt = getData("master_". $master . "_password_salt");
      
      $items = $items == null ? array() : json_decode($items, true);
      
      $items[$key] = $value;
      $items = json_encode($items);
      
      $key = $this->master_salt . $password . $pass_salt;
      Crypto::$KEY_BYTE_SIZE = mb_strlen($key, '8bit');
      $items = Crypto::encrypt(base64_encode($items), $key);
      
      $items = base64_encode($items);
      saveData("master_". $master ."_items", $items);
      return true;
    }else{
      return false;
    }
  }
  
  public function KeyGet($master, $password, $key){
    if($this->MasterExists($master)){
      require_once APP_DIR . "/src/Inc/Crypto.php";
      
      $items = getData("master_". $master ."_items");
      $pass_salt = getData("master_". $master . "_password_salt");
      
      $encrypt_key = $this->master_salt . $password . $pass_salt;
      Crypto::$KEY_BYTE_SIZE = mb_strlen($encrypt_key, '8bit');
      $items = base64_decode(Crypto::decrypt(base64_decode($items), $encrypt_key));
      $items = str_replace("&quot;", "'", $items);

      $items = $items == null ? array() : json_decode($items, true);
      return isset($items[$key]) ? $items[$key] : null;
    }else{
      return false;
    }
  }
}
