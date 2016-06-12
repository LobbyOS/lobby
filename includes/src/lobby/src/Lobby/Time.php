<?php
namespace Lobby;

use Lobby\DB;

class Time {
  
  private static $tz = "UTC";
  
  public static function __constructStatic(){
    /**
     * Default timezone of Lobby is UTC
     */
    date_default_timezone_set("UTC");
    if(DB::getType() === "mysql"){
      $sql = DB::getDBH()->prepare("SET time_zone = ?;");
      $sql->execute(array('+00:00'));
    }
    self::loadConfig();
  }
  
  public static function loadConfig(){
    $tz = DB::getOption("lobby_timezone");
    if($tz !== null){
      self::$tz = $tz;
    }
  }
  
  /**
   * Return a "Y-m-d H:i:s" Timestamp
   */
  public static function now($format = "Y-m-d H:i:s"){
    $date = new \DateTime("now", new \DateTimeZone(self::$tz));
    return $date->format($format);
  }
  
  public static function date($date, $format = "Y-m-d H:i:s"){
    $date = new \DateTime($date, new \DateTimeZone("UTC"));
    $date->setTimeZone(new \DateTimeZone(self::$tz));
    return $date->format($format);
  }
  
  public static function getTimezone($offset){
    foreach (\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $timezone) {
      $datetime = new \DateTime("now", new \DateTimeZone($timezone));
  
      // find a timezone matching the offset and abbreviation
      if ($offset == $datetime->format('P')) {
        return $timezone;
      }
    }
  }

}
