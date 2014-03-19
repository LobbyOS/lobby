<?
include($LC->root."config.php");
if(isset($LC_config) && count($LC_config)!=0){
 foreach ($LC_config as $engine=>$valie){
    if (!defined("L_DB_CONFIG_".strtoupper($engine))){
        define("L_DB_CONFIG_".strtoupper($engine),$valie);
    }
 }
 define("L_DB_CONFIG_SET", true);
}else{
 define("L_DB_CONFIG_SET", false);
}
class db extends L{
 private $dbhost;
 private $dbuser;
 private $dbpass;
 private $dbname;
 private $dbport;
 private $prefix;
 private $dbh;
 private $error;
 public $db = false;
 
 function __construct(){
  global $LC_config;
  $root=$GLOBALS['LC']->root;
  if(file_exists($root."config.php") && L_DB_CONFIG_SET){
   $this->dbhost = L_DB_CONFIG_HOST;
   $this->dbuser = L_DB_CONFIG_USER;
   $this->dbpass = L_DB_CONFIG_PASS;
   $this->dbname = L_DB_CONFIG_DB;
   $this->dbport = L_DB_CONFIG_PORT;
   $this->prefix = L_DB_CONFIG_PREFIX;
   $options = array(
          PDO::ATTR_PERSISTENT    => true,
          PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
   );
   try{
    $this->dbh=new PDO("mysql:dbname={$this->dbname};host={$this->dbhost};port={$this->dbport}", $this->dbuser, $this->dbpass, $options);
    $notable=0;
    $tables=array("options", "data");
    foreach($tables as $v){
     $results = $this->dbh->prepare("SHOW TABLES LIKE ?");
     $results->execute(array("{$this->prefix}$v"));
     if(!$results || $results->rowCount()==0) {
      $notable=1;
     }
    }
    if($notable==0){
     $this->db=true;
    }else{
     $this->error = "Lobby Tables Not Found";
    }
   }catch( PDOException $e ){
    $this->db=false;
    $this->error = $e->getMessage();
   }
  }else{
   $this->db=false;
  }
 }
 function filt(&$value) {
  if($value != strip_tags($value)){
   $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
  return $value;
 }
 public function prepare($sql){
  if($this->db && !preg_match("/(?:DROP|drop)/", $sql)){
   return $this->dbh->prepare($sql);
  }else{
   return false;
  }
 }
 function getOption($name){
  if($this->db){
   $sql=$this->prepare("SELECT `val` FROM {$this->prefix}options WHERE name=?");
   $sql->execute(array($name));
   $return=$this->filt($sql->fetchColumn());
   return $return;
  }
 }
 function saveOption($name, $value){
  if($this->db){
   $sql=$this->prepare("SELECT COUNT(`name`) FROM {$this->prefix}options WHERE `name`=?");
   $sql->execute(array($name));
   if($sql->fetchColumn()!=0){
    $sql=$this->prepare("UPDATE {$this->prefix}options SET val=? WHERE `name`=?");
    $sql->execute(array($value, $name));
    return $sql->execute(array($value, $name));
   }else{
    $sql=$this->prepare("INSERT INTO {$this->prefix}options (name,val) VALUES (?, ?)");
    return $sql->execute(array($name, $value));
   }
  }
 }
 function getData($id, $name=""){
  if($this->db){
   if($id!="" && $name==""){
    $sql=$this->prepare("SELECT `content`, `name`, `updated` FROM {$this->prefix}data WHERE app=?");
    $sql->execute(array($id));
    $return=$sql->fetchAll();
   }else{
    $sql=$this->prepare("SELECT `content`, `name`, `updated` FROM {$this->prefix}data WHERE name=? AND app=?");
    $sql->execute(array($name, $id));
    $return=$sql->fetchAll();
   }
   array_walk_recursive($return, array($this, "filt"));
   return count($return)==0 ? false:$return;
  }
 }
 function saveData($id, $name="", $value=""){
  $App=new App($id);
  if($this->db && $App->exists){
   $sql=$this->prepare("SELECT COUNT(`name`) FROM {$this->prefix}data WHERE `name`=? AND `app`=?");
   $sql->execute(array($name, $id));
   if($sql->fetchColumn()!=0){
    $sql=$this->prepare("UPDATE {$this->prefix}data SET `content`=? WHERE `name`=? AND `app`=?");
    $sql->execute(array($value, $name, $id));
    return true;
   }else{
    $sql=$this->prepare("INSERT INTO {$this->prefix}data (app, name, content) VALUES (?, ?, ?)");
    return $sql->execute(array($id, $name, $value));
   }
  }
 }
 function removeData($id="", $name=""){
  if($this->db){
   if($name!="" && $id!=""){
    $sql=$this->prepare("DELETE FROM {$this->prefix}data WHERE name=? AND app=?");
    $sql->execute(array($name, $id));
    return true;
   }
  }
 }
}
?>
