<?php
namespace Lobby\App;
class fr_logsys extends \Lobby\App {
  
  public $set = false;
  public $info, $dbinfo = array();
  public $table, $dbh = null;
  
  public function page(){
    $this->dbinfo = \H::getJSONData("credentials");
    if(getData("credentials") != null && $this->connect($this->dbinfo)){
      $this->set = true;
    }
    return "auto";
  }
  
  public function load(){
    $dbinfo = \H::getJSONData("credentials");
    $this->table = $dbinfo['db_table'];
    require_once APP_DIR . "/src/Inc/logsys.config.php";
  }
  
  public function setInfo(){
    $this->load();
    
    $number_of_users = \fr_logsys\Fr\LS::$dbh->query("SELECT COUNT(1) FROM `". $this->table ."`")->fetchColumn();
    $number_of_tokens = \fr_logsys\Fr\LS::$dbh->query("SELECT COUNT(1) FROM `resetTokens`")->fetchColumn();
    
    $this->info = array(
      "users" => $number_of_users,
      "verify_tokens" => $number_of_tokens
    );
  }
  
  public function connect($credentials){
    $config = array_merge(array(
      "db_name" => "",
      "db_host" => "",
      "db_port" => "",
      "db_username" => "",
      "db_password" => "",
      "db_table" => ""
    ), $credentials);
    
    try{
      $this->dbh = new \PDO("mysql:dbname={$config['db_name']};host={$config['db_host']};port={$config['db_port']};charset=utf8", $config['db_username'], $config['db_password']);
      /**
       * SQL Injection Vulnerable.
       */
      $table = htmlspecialchars($config['db_table']);
      $sql = $this->dbh->prepare("SELECT 1 FROM `". $table ."` LIMIT 1");
      $sql->execute();
      
      if($sql->rowCount() == 0){
        return "no_table";
      }else{
        return true;
      }
    }catch(\PDOException $e){
      return false;
    }
  }
  
  public function registeredInAMonth(){
    $sql = $this->dbh->prepare("SELECT COUNT(1) FROM `{$this->table}` WHERE YEAR(`created`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(`created`) > MONTH(CURRENT_DATE - INTERVAL 1 MONTH)");
    $sql->execute();
    return $sql->fetchColumn();
  }
}
