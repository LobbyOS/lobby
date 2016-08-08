<?php
namespace Lobby;

/**
 * DB = DataBase
 * Handling of Database
 */

class DB extends \Lobby {
  
  protected static $prefix = "", $dbh;
  
  /**
   * The DBMS begin used - MySQL or SQLite
   */
  protected static $type;
 
  public static function __constructStatic(){
    /**
     * Get DB config
     */
    $config = \Lobby::config(true);
    
    if(is_array($config)){
      /**
       * Make DB credentials variables from the config.php file
       */
      self::$prefix = $config['prefix'];
      self::$type = $config['type'];
     
      $options = array(
        \PDO::ATTR_PERSISTENT => true,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
      );
      try{
        if($config['type'] === 'mysql'){
          self::$dbh = new \PDO("mysql:dbname={$config['dbname']};host={$config['host']};port={$config['port']};charset=utf8;", $config['username'], $config['password'], $options);
          
          /**
           * Check if Lobby tables exist
           */
          $notable = false;
          $tables = array("options", "data"); // The Tables of Lobby
          foreach($tables as $tableName){
            $results = self::$dbh->prepare("SHOW TABLES LIKE ?");
            $results->execute(array(self::$prefix . $tableName));
            if($results->rowCount() == 0) {
              $notable = true;
            }
          }
        }else if($config['type'] === 'sqlite'){
          self::$dbh = new \PDO("sqlite:" . \Lobby\FS::loc($config['path']), "", "", $options);
          
          /**
           * Enable Multithreading Read/Write
           */
          self::$dbh->exec("PRAGMA journal_mode=WAL;");
          
          /**
           * Check if Lobby tables exist
           */
          $sql = self::$dbh->query("SELECT COUNT(1) FROM `sqlite_master` WHERE `type` = 'table' AND (`name` = 'l_data' OR `name` = 'l_options')");
          $notable = $sql->fetchColumn() === "2" ? false : true;
        }

        if($notable === false){ /* There are database tables */
          parent::$installed = true;
        }else{
          parent::log(array(
            "fatal",
            "Tables required by Lobby was not found in the database. Check your <b>config.php</b> and database to fix the error. Or Install again by removing <b>config.php</b>."
          ));
        }
      }catch(\PDOException $e){
        parent::$installed = false;
        $error = $e->getMessage();

        parent::log(array(
          "fatal",
          "Unable to connect to database server. Is the database credentials given in <b>config.php</b> correct ? <blockquote>$error</blockquote>"
        ));
      }
    }else{
      self::$installed = false;
    }
  }
  
  /**
   * A HTML Filter function
   */
  public static function filt(&$value) {
    if($value != strip_tags($value)){
      $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    return $value;
  }
  
  /**
   * Get option value
   */
  public static function getOption($name){
    if(self::$installed){
      $sql = self::$dbh->prepare("SELECT `value` FROM `". self::$prefix ."options` WHERE `name` = ?");
      $sql->execute(array($name));
      $r = $sql->fetchColumn();

      if($r !== false){
        $column = $r;
        $return = self::filt($column);
      }else{
        $return = null;
      }
      return $return;
    }
  }
  
  /**
   * Save option
   */
  public static function saveOption($name, $value){
    if(self::$installed && $value != null){
      $sql = self::$dbh->prepare("SELECT COUNT(*) FROM `". self::$prefix ."options` WHERE `name` = ?");
      $sql->execute(array($name));
      if($sql->fetchColumn() === "0"){
        $sql = self::$dbh->prepare("INSERT INTO `". self::$prefix ."options` (`name`, `value`) VALUES (?, ?)");
        return $sql->execute(array($name, $value));
      }else{
        $sql = self::$dbh->prepare("UPDATE `". self::$prefix ."options` SET `value` = ? WHERE `name` = ?");
        return $sql->execute(array($value, $name));
      }
    }else{
      return false;
    }
  }
  
  /**
   * Retrieve JSON Value stored as option as Array
   */
  public static function getJSONOption($key){
    $json = self::getOption($key);
    $json = json_decode($json, true);
    return is_array($json) ? $json : array();
  }
  
  /**
   * Save JSON Data in options
   */
  public static function saveJSONOption($key, $values){
    $old = self::getJSONOption($key);
    
    $new = array_replace_recursive($old, $values);
    foreach($values as $k => $v){
      if($v === false){
        unset($new[$k]);
      }
    }
    $new = json_encode($new, JSON_HEX_QUOT | JSON_HEX_TAG);
    self::saveOption($key, $new);
    return true;
  }
  
  /**
   * Get App Data
   */
  public static function getData($id, $name = "", $extra = false, $safe = true){
    if(self::$installed){
      $return = array();
      $prefix = self::$prefix;
      if($id != "" && $name == ""){
        $sql = self::$dbh->prepare("SELECT * FROM `{$prefix}data` WHERE `app` = ?");
        $sql->execute(array($id));
        $return = $sql->fetchAll();
        foreach($return as &$v){
          $v["created"] = \Lobby\Time::date($v["created"]);
          $v["updated"] = \Lobby\Time::date($v["updated"]);
        }
      }else{
        $sql = self::$dbh->prepare("SELECT * FROM `{$prefix}data` WHERE `name` = ? AND `app` = ?");
        $sql->execute(array($name, $id));
        $r = $sql->fetchAll(\PDO::FETCH_ASSOC);
        $count = count($r);

        if($count > 1){
          /**
           * Multiple Results; so give a multidimensional array of results
           */
          $return = $r;
          foreach($return as &$v){
            $v["created"] = \Lobby\Time::date($v["created"]);
            $v["updated"] = \Lobby\Time::date($v["updated"]);
          }
        }else if($count === 1){
          /**
           * A single result is present, so give a single array only if $extra is TRUE
           */
          $return = $r[0];
          if($extra === false){
            $return = $return['value'];
          }else{
            /**
             * Cconvert time to the timezone chosen by user
             */
            $return["created"] = \Lobby\Time::date($return["created"]);
            $return["updated"] = \Lobby\Time::date($return["updated"]);
          }
        }else{
          $return = array();
        }
      }
      if(is_array($return) && $safe === true){
        array_walk_recursive($return, function(&$value){
          $value = \Lobby\DB::filt($value);
        });
      }
      return is_array($return) && count($return) == 0 ? null : $return;
    }
  }
  
  /**
   * Save App Data
   */
  public static function saveData($appID, $key, $value = ""){
    if(self::$installed && \Lobby\Apps::exists($appID) && $key != ""){
      $sql = self::$dbh->prepare("SELECT COUNT(`name`) FROM `". self::$prefix ."data` WHERE `name` = ? AND `app`=?");
      $sql->execute(array($key, $appID));
     
      if($sql->fetchColumn() != 0){
        $sql = self::$dbh->prepare("UPDATE `". self::$prefix ."data` SET `value` = ?, `updated` = CURRENT_TIMESTAMP WHERE `name` = ? AND `app` = ?");
        $sql->execute(array($value, $key, $appID));
        return true;
      }else{
        $sql = self::$dbh->prepare("INSERT INTO `". self::$prefix ."data` (`app`, `name`, `value`, `created`, `updated`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        return $sql->execute(array($appID, $key, $value));
      }
    }else{
      return false;
    }
  }
  
  /**
   * Remove App Data
   */
  public static function removeData($appID = "", $keyName){
    if(self::$installed){
     if($keyName != "" && $appID != ""){
       $sql = self::$dbh->prepare("DELETE FROM `". self::$prefix ."data` WHERE `name`=? AND `app`=?");
       $sql->execute(array($keyName, $appID));
       return true;
     }
    }
  }
  
  /**
   * Get database handler
   */
  public static function getDBH(){
    return self::$dbh;
  }
  
  /**
   * DBMS used
   */
  public static function getType(){
    return self::$type;
  }
  
  public static function getPrefix(){
    return self::$prefix;
  }
  
}
