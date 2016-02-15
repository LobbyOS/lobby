<?php
namespace Lobby;

class Time {
  
  private static $tz = "UTC";
  
  public static function init(){
    /**
     * Default timezone of Lobby is UTC
     */
    date_default_timezone_set("UTC");
    if(\Lobby\DB::$type === "mysql"){
      $sql = \Lobby\DB::$dbh->prepare("SET time_zone = ?;");
      $sql->execute(array('UTC+0'));
    }
    self::loadConfig();
  }
  
  public static function loadConfig(){
    $tz = \Lobby\DB::getOption("lobby_timezone");
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

}
\Lobby\Time::init();
