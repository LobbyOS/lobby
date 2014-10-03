<?php
if(file_exists(L_ROOT . "/config.php")){
	include(L_ROOT . "/config.php");
}

if(isset($LC_config) && count($LC_config)!=0){
 	foreach ($LC_config as $key => $value){
    	if (!defined("L_DB_CONFIG_".strtoupper($key))){
      	define("L_DB_CONFIG_".strtoupper($key), $value);
    	}
 	}
 	define("L_DB_CONFIG_SET", true);
}else{
 	define("L_DB_CONFIG_SET", false);
}

class db extends L {
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
  		$root = L_ROOT;

  		if( file_exists($root . "/config.php") && L_DB_CONFIG_SET ){
  			
  			/* Make DB credentials variables from the config.php file */
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
    			$this->dbh = new PDO("mysql:dbname={$this->dbname};host={$this->dbhost};port={$this->dbport}", $this->dbuser, $this->dbpass, $options);
    			$notable = false;
    			$tables  = array("options", "data"); // The Tables of Lobby
    			foreach($tables as $tableName){
     				$results = $this->dbh->prepare("SHOW TABLES LIKE ?");
     				$results->execute(array("{$this->prefix}$tableName"));
     				if(!$results || $results->rowCount() == 0) {
      				$notable = true;
     				}
    			}
    			if(!$notable){ /* There are database tables */
     				$this->db = true;
    			}else{
     				$this->error = "Lobby Tables Not Found";
    			}
   		}catch( PDOException $e ){
    			$this->db	 = false;
    			$this->error = $e->getMessage();
   		}
  		}else{
   		$this->db = false;
  		}
 	}
 	
 	/* A HTML Filter function */
 	function filt(&$value) {
  		if($value != strip_tags($value)){
			$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  		}
  		return $value;
 	}
 	
 	/* Equivalent of PDO::prepare but with security */
 	public function prepare($sql){
  		if($this->db && !preg_match("/(?:DROP|drop)/", $sql)){
   		return $this->dbh->prepare($sql);
  		}else{
   		return false;
  		}
 	}
 	
 	/* Get option value */
 	function getOption($name){
  		if($this->db){
   		$sql = $this->prepare("SELECT `val` FROM {$this->prefix}options WHERE name=?");
   		$sql->execute(array($name));
   		$column = $sql->fetchColumn();
   		$return = $this->filt($column);
   		return $return;
  		}
 	}
 	
 	/* Save option */
 	function saveOption($name, $value){
  		if($this->db && $value!=null){
   		$sql = $this->prepare("SELECT COUNT(`name`) FROM {$this->prefix}options WHERE `name`=?");
   		$sql->execute(array($name));
   		if($sql->fetchColumn()!=0){
    			$sql = $this->prepare("UPDATE {$this->prefix}options SET val=? WHERE `name`=?");
    			return $sql->execute(array($value, $name));
   		}else{
    			$sql = $this->prepare("INSERT INTO {$this->prefix}options (name,val) VALUES (?, ?)");
    			return $sql->execute(array($name, $value));
   		}
  		}else{
   		return false;
  		}
 	}
 	
 	/* Get App Data */
 	function getData($id, $name="", $safe = true){
  		if($this->db){
			$return = array();
			if($id != "" && $name == ""){
    			$sql = $this->prepare("SELECT `content`, `name`, `updated` FROM `{$this->prefix}data` WHERE `app` = ?");
    			$sql->execute(array($id));
    			$return = $sql->fetchAll();
			}else{
    			$sql = $this->prepare("SELECT `content`, `name`, `updated` FROM `{$this->prefix}data` WHERE `name` = ? AND `app` = ?");
    			$sql->execute(array($name, $id));
    			if($sql->rowCount() > 1){
    				/* Multiple Results; so give a multidimensional array of results */
    				$return = $sql->fetchAll();
    			}else{
    				/* A single result is present, so give a single array */
    				$return = $sql->fetch(PDO::FETCH_ASSOC);
    			}
			}
			if(is_array($return) && $safe){
				array_walk_recursive($return, array($this, "filt"));
			}
			return count($return) == 0 ? false : $return;
  		}
 	}
 	
 	/* Save App Data */
 	function saveData($appID, $key="", $value=""){
  		$App = new App($appID);
  		if($this->db && $App->exists && $key!=""){
   		$sql = $this->prepare("SELECT COUNT(`name`) FROM `{$this->prefix}data` WHERE `name`=? AND `app`=?");
   		$sql->execute(array($key, $appID));
   		if($sql->fetchColumn() != 0){
    			$sql = $this->prepare("UPDATE {$this->prefix}data SET `content`=? WHERE `name`=? AND `app`=?");
    			$sql->execute(array($value, $key, $appID));
    			return true;
   		}else{
    			$sql = $this->prepare("INSERT INTO `{$this->prefix}data` (`app`, `name`, `content`) VALUES (?, ?, ?)");
    			return $sql->execute(array($appID, $key, $value));
   		}
  		}else{
  			return false;
  		}
 	}
 	
 	/* Remove App Data */
 	function removeData($appID="", $keyName=""){
  		if($this->db){
   		if($keyName != "" && $appID != ""){
    			$sql = $this->prepare("DELETE FROM `{$this->prefix}data` WHERE `name`=? AND `app`=?");
    			$sql->execute(array($keyName, $appID));
    			return true;
   		}
  		}
 	}
}
?>