<?php
namespace Lobby;

class DB extends \Lobby {
  
  public static $prefix, $dbh = "";
 
  public static function init(){
    $root = L_DIR;
    $config = \Lobby::config(true);
    if(is_array($config)){
      /**
       * Make DB credentials variables from the config.php file
       */
      self::$prefix = $config['prefix'];
     
      $options = array(
        \PDO::ATTR_PERSISTENT => true,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
      );
      try{
        self::$dbh = new \PDO("mysql:dbname={$config['dbname']};host={$config['host']};port={$config['port']};charset=utf8;", $config['username'], $config['password'], $options);
        
        $notable = false;
        $tables = array("options", "data"); // The Tables of Lobby
        foreach($tables as $tableName){
          $results = self::$dbh->prepare("SHOW TABLES LIKE ?");
          $results->execute(array(self::$prefix . $tableName));
          if($results->rowCount() == 0) {
            $notable = true;
          }
        }
        
        if($notable === false){ /* There are database tables */
          parent::$installed = true;
        }else{
          self::$error = "Lobby Tables Not Found";
          self::log("Lobby Tables not found in database. Install Again.");
        }
      }catch(\PDOException $e){
        parent::$installed = false;
        $error = $e->getMessage();
        self::$error = $error;
        $GLOBALS['initError'] = array("Couldn't Connect To Database", "Unable to connect to database server. Is the credentials given in <b>config.php</b> correct ? <blockquote>". $error ."</blockquote>");
        self::log("Unable to connect to database server : ". $error);
      }
    }else{
      self::$installed = false;
    }
  }
  
  /* A HTML Filter function */
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
      
      if($sql->rowCount() != 0){
        $column = $sql->fetchColumn();
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
   if(self::$installed&& $value != null){
     $sql = self::$dbh->prepare("SELECT COUNT(`name`) FROM `". self::$prefix ."options` WHERE `name` = ?");
     $sql->execute(array($name));
     if($sql->fetchColumn() != 0){
       $sql = self::$dbh->prepare("UPDATE `". self::$prefix ."options` SET `value` = ? WHERE `name` = ?");
       return $sql->execute(array($value, $name));
     }else{
       $sql = self::$dbh->prepare("INSERT INTO `". self::$prefix ."options` (`name`, `value`) VALUES (?, ?)");
       return $sql->execute(array($name, $value));
     }
    }else{
      return false;
    }
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
      }else{
        $sql = self::$dbh->prepare("SELECT * FROM `{$prefix}data` WHERE `name` = ? AND `app` = ?");
        $sql->execute(array($name, $id));
        if($sql->rowCount() > 1){
          /**
           * Multiple Results; so give a multidimensional array of results
           */
          $return = $sql->fetchAll();
        }else{
          /**
           * A single result is present, so give a single array only if $extra is TRUE
           */
          if($sql->rowCount() != 0){
            $return = $sql->fetch(\PDO::FETCH_ASSOC);
            if($extra === false){
              $return = $return['value'];
            }
          }else{
            $return = array();
          }
        }
      }
      if(is_array($return) && $safe === true){
        array_walk_recursive($return, function(&$c){ $c = self::filt($c); });
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
        $sql = self::$dbh->prepare("UPDATE `". self::$prefix ."data` SET `content` = ?, `updated` = NOW() WHERE `name` = ? AND `app` = ?");
        $sql->execute(array($value, $key, $appID));
        return true;
      }else{
        
        $sql = self::$dbh->prepare("INSERT INTO `". self::$prefix ."data` (`app`, `name`, `content`, `created`, `updated`) VALUES (?, ?, ?, NOW(), NOW())");
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
}
\Lobby\DB::init();
?>
