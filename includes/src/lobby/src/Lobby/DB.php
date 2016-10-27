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
   * Get database handler
   * @return \PDO DBH
   */
  public static function getDBH(){
    return self::$dbh;
  }

  /**
   * DBMS used
   * @return string mysql or sqlite
   */
  public static function getType(){
    return self::$type;
  }

  /**
   * Get prefix of table names
   * @return string Prefix
   */
  public static function getPrefix(){
    return self::$prefix;
  }

}
