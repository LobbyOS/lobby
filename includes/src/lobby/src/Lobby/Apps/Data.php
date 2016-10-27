<?php
namespace Lobby\Apps;

use Lobby\App;
use Lobby\DB;

/**
 * Manage data stored by app
 */
class Data {

  /**
   * @var Lobby\App Object of app running this
   */
  private $app;

  /**
   * @var \PDO Database Handler
   */
  private $dbh;

  /**
   * @var string The prefix of table names
   */
  private $dbPrefix;

  public function __construct(App $App){
    $this->app = $App;
    $this->dbh = DB::getDBH();
    $this->dbPrefix = DB::getPrefix();
  }

  /**
   * Save a key-value pair
   * @param  string $key   Key by which data will be accessed
   * @param  string $value Data to save
   * @return bool          Whether operation is successful
   */
  public function saveValue($key, $value){
    $sth = $this->dbh->prepare("SELECT COUNT(`name`) FROM `". $this->dbPrefix ."data` WHERE `name` = ? AND `app`=?");
    $sth->execute(array($key, $this->app->id));

    if($sth->fetchColumn() != 0){
      $sth = $this->dbh->prepare("UPDATE `". $this->dbPrefix ."data` SET `value` = ?, `updated` = CURRENT_TIMESTAMP WHERE `name` = ? AND `app` = ?");
      $sth->execute(array($value, $key, $this->app->id));
      return true;
    }else{
      $sth = $this->dbh->prepare("INSERT INTO `". $this->dbPrefix ."data` (`app`, `name`, `value`, `created`, `updated`) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
      return $sth->execute(array($this->app->id, $key, $value));
    }
  }

  /**
   * Get a value associated with key
   * @param  [type]  $key  Key
   * @param  boolean $meta Whether meta data should be included in return value.
   *                       If this is set to true, the return value would be an array
   * @return string|array  Value or Value + metadata. Meta data would include the
   *                       value created and updated timestamp.
   */
  public function getValue($key, $meta = false){
    $sth = $this->dbh->prepare("SELECT * FROM `{$this->dbPrefix}data` WHERE `name` = ? AND `app` = ?");
    $sth->execute(array($key, $this->app->id));

    $r = $sth->fetchAll(\PDO::FETCH_ASSOC);
    if(count($r) === 1){
      /**
       * A single result is present, so give a single array only if $extra is TRUE
       */
      $return = $r[0];
      if($meta === false){
        $return = $return['value'];
      }else{
        /**
         * Cconvert time to the timezone of user
         */
        $return["created"] = \Lobby\Time::date($return["created"]);
        $return["updated"] = \Lobby\Time::date($return["updated"]);
      }
    }else{
      $return = array();
    }

    return empty($return) ? null : $return;
  }

  /**
   * Get all values stored by app
   * @return array Values along with metadata
   */
  public function getValues(){
    $sth = $this->dbh->prepare("SELECT * FROM `{$this->dbPrefix}data` WHERE `app` = ?");
    $sth->execute(array($this->app->id));

    $return = $sth->fetchAll();
    foreach($return as &$v){
      $v["created"] = \Lobby\Time::date($v["created"]);
      $v["updated"] = \Lobby\Time::date($v["updated"]);
    }
    return $return;
  }

  /**
   * Remove a key-value pair
   * @param  string $key Key to remove
   * @return bool        Whether data was removed
   */
  public function remove($key){
    $sql = $this->dbh->prepare("DELETE FROM `{$this->dbPrefix}data` WHERE `name` = ? AND `app` = ?");
    $sql->execute(array($key, $this->app->id));
    return true;
  }

  /**
   * Get stored array value
   * @param  string $key Key
   * @return array       The array data saved
   */
  public function getArray($key){
    $data = $this->getValue($key, false);
    $data = json_decode($data, true);
    return is_array($data) ? $data : array();
  }

  /**
   * Save an array
   * @param  string $key  Key
   * @param  array  $value Array of data to save. To remove an item
   *                      in array, set the value of it to boolean FALSE.
   * @return bool         Whether array is saved
   */
  public function saveArray($key, $value){
    $existingArray = $this->getArray($key);
    $newArray = array_replace_recursive($existingArray, $value);

    foreach($value as $k => $v){
      /**
       * Remove key-value pairs which have FALSE as value
       */
      if($v === false){
        unset($newArray[$k]);
      }
    }

    $newArray = json_encode($newArray);
    $this->saveValue($key, $newArray);
    
    return true;
  }

}
