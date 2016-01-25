<?php
namespace Lobby;

/**
 * The class for installing Lobby
 * Contains Database & Software Creation
 */
class Install extends \Lobby {

  private static $database = array();
  private static $dbh;

  /**
   * The $checking parameter tells if any Success output should be made or not.
   * Default : Visible. It's named $checking, because output shouldn't be made
   * while checking DB connection. So, if it's a call made while checking,
   * then the parameter $checking must be TRUE.
   * ----------
   * We don't have a step2 function because there is no Step 3
   */
  public static function step1(){
    if(!is_writable(L_DIR)){
      ser("Error", "Lobby Directory is not Writable. Please set <blockquote>" . L_DIR . "</blockquote> directory's permission to writable.<cl/><a href='install.php?step=1' class='button'>Check Again</a>");
      return false;
    }elseif(\Lobby\FS::exists("/config.php")){
      ser("config.php File Exists", "A config.php file already exitsts in <blockquote>". L_DIR ."</blockquote> directory. Remove it and try again. <cl/><a href='install.php?step=1". \H::csrf('g') ."' class='button'>Check Again</a>");
      return false;
    }else{
      return true;
    }
  }
  
  /**
   * Check if the credentials given can be used to establish a
   * connection with the DB server
   */
  public static function checkDatabaseConnection(){
    try {
      $db = new \PDO("mysql:host=". self::$database['host'] .";port=". self::$database['port'], self::$database['username'], self::$database['password'], array(
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
      ));
      self::$dbh = $db;
      self::$dbh->exec("CREATE DATABASE IF NOT EXISTS `" . self::$database['dbname'] . "`");
      self::$dbh->query("USE `" . self::$database['dbname'] . "`");
      
      $notable = false;
      $tables = array("options", "data"); // The Tables of Lobby
      foreach($tables as $tableName){
        $results = self::$dbh->prepare("SHOW TABLES LIKE ?");
        $results->execute(array(self::$database['prefix'] . $tableName));
        if(!$results || $results->rowCount() == 0) {
          $notable = true;
        }
      }
        
      if(!$notable){ /* There are database tables */
        ser("Error", "Lobby Tables with prefix <b>". self::$database['prefix'] ."</b> exists. Delete (DROP) those tables and <a href='install.php?step=2". \H::csrf("g") ."'>try again.</a>");
        return false;
      }
    }catch(\PDOException $Exception) {
      ser("Error", "Unable to connect. Make sure that the settings you entered are correct. <cl/><a href='install.php?step=2'>Try Again</a>");
      return false;
    }
  }
  
  /**
   * Make the config.php file
   */
  public static function makeConfigFile(){
    $lobbyID = \H::randStr(10) . \H::randStr(15) . \H::randStr(20); // Lobby Global ID
    $lobbySID   = hash("sha512", \H::randStr(15) . \H::randStr(30)); // Lobby Secure ID
    $configFileLoc = L_DIR . "/config.php";
    $cfg = self::$database;
    
    /* Make the configuration file */
    $config_sample = \Lobby\FS::get("/includes/lib/lobby/inc/config-sample.php");
    $config_file   = $config_sample;
    $config_file   = preg_replace("/host'(.*?)'(.*?)'/", "host'$1'{$cfg['host']}'", $config_file);
    $config_file   = preg_replace("/port'(.*?)'(.*?)'/", "port'$1'{$cfg['port']}'", $config_file);
    $config_file   = preg_replace("/username'(.*?)''/", "username'$1'{$cfg['username']}'", $config_file);
    $config_file   = preg_replace("/password'(.*?)''/", "password'$1'{$cfg['password']}'", $config_file);
    $config_file   = preg_replace("/dbname'(.*?)''/", "dbname'$1'{$cfg['dbname']}'", $config_file);
    $config_file   = preg_replace("/lobbyID'(.*?)''/", "lobbyID'$1'{$lobbyID}'", $config_file);
    $config_file   = preg_replace("/secureID'(.*?)''/", "secureID'$1'{$lobbySID}'", $config_file);
    $config_file   = preg_replace("/prefix'(.*?)'(.*?)'/", "prefix'$1'{$cfg['prefix']}'", $config_file);
    
    /**
     * Create the config.php file
     */
    if(\Lobby\FS::write($configFileLoc, $config_file) === false){
      ser("Failed Creating Config File", "Something happened while creating the file. Perhaps it was something that you did ?");
    }else{
      chmod(L_DIR . "/config.php", 0550);
    }
  }
  
  /* Create Tables in the DB */
  public static function makeDatabase($prefix){
    try {
      /* Create Tables */
      $sql = self::$dbh->prepare("
        CREATE TABLE IF NOT EXISTS `{$prefix}options` (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `name` varchar(64) NOT NULL,
          `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
        CREATE TABLE IF NOT EXISTS `{$prefix}data` (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `app` varchar(50) NOT NULL,
          `name` varchar(150) NOT NULL,
          `value` longblob NOT NULL,
          `created` datetime NOT NULL,
          `updated` datetime NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"
      );
      $sql->execute();

      /* Insert The Default Data In To Tables */
      $lobby_info = \Lobby\FS::get("/lobby.json");
      $lobby_info = json_decode($lobby_info, true);
      $sql = self::$dbh->prepare("
        INSERT INTO `{$prefix}options`
          (`id`, `name`, `value`)
        VALUES
          (NULL, 'lobby_version', ?),
          (NULL, 'lobby_version_release', ?);"
      );
      $sql->execute(array($lobby_info['version'], $lobby_info['released']));
    return true;
    }catch(\PDOException $Exception){
      self::log("Install error : " . $Exception->getMessage());
      return false;
    }
  }

  public static function dbConfig($array){
    self::$database = $array;
  }
  
  /* After installation, check if Lobby installed directory is safe */
  public static function safe(){
    $configFile = L_DIR . "/config.php";
    if(is_writable($configFile)){
      return "configFile";
    }else{
      return true;
    }
  }
}
