<?php
namespace Lobby\UI;

use Hooks;
use Lobby\DB;

/**
 * Makes the Top Panel
 */
class Panel {

  public static $topItems = array(
    "left" => array(),
    "right" => array()
  );
  
  private static $panelItemFormat = array(
    "text" => null,
    "href" => null,
    "html" => null,
    "subItems" => array(),
    "position" => "left"
  );
  
  public static $notifyItem = array(
    "contents" => null,
    "href" => null,
    "icon" => null,
    "iconURL" => null
  );
  
  public static function addTopItem($name, $array){
    $array = array_replace_recursive(self::$panelItemFormat, $array);
    $loc = $array['position'];
    
    if($loc === "right"){
      array_reverse($array);
    }

    /**
     * Check if the item is already registered
     * --
     * If the item is already registered, then replace it
     * else, register new one
     */
    if(isset(self::$topItems[$loc][$name])){
      $merged = array_merge(self::$topItems[$loc][$name], $array);
      self::$topItems[$loc][$name] = $merged;
    }else{
      $originalArr = isset(self::$topItems[$loc]) ? self::$topItems[$loc] : array();
      $merged = array_merge($originalArr, array(
        $name => $array
      ));
      self::$topItems[$loc] = $merged;
    }
  }
  
  public static function removeTopItem($name, $position){
    if(isset(self::$topItems[$position][$name]))
      unset(self::$topItems[$position][$name]);
  }
  
  public static function getPanelItems($side = "left"){
    $items = self::$topItems;
    if($side === "right"){
      ksort($items['right']);
    }
    $items[$side] = Hooks::applyFilters("panel.{$side}.items", $items[$side]);
    return $items[$side];
  }
  
  /**
   * Push an item to Notify
   */
  public static function addNotifyItem($id, $info){
    DB::saveJSONOption("notify_items", array(
      $id => array_replace_recursive(self::$notifyItem, $info)
    ));
  }
  
  /**
   * Remove an item from Notify
   */
  public static function removeNotifyItem($id){
    DB::saveJSONOption("notify_items", array(
      $id => false
    ));
  }

}
