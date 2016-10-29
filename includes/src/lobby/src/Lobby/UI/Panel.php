<?php
namespace Lobby\UI;

use Hooks;
use Lobby\DB;

/**
 * Manage panels
 */
class Panel {

  /**
   * @var array Top panel"s items
   */
  public static $topItems = array(
    "left" => array(),
    "right" => array()
  );

  /**
   * @var array Left panel"s items
   */
  public static $leftItems = array(
    "top" => array(),
    "bottom" => array()
  );

  /**
   * @var array Raw skeleton of a Top Panel item
   */
  private static $panelTopItemFormat = array(
    "text" => null,
    "href" => null,
    "html" => null,
    "class" => null,
    "subItems" => array(),
    "position" => "left"
  );

  /**
   * @var array Raw skeleton of a Left Panel item
   */
  private static $panelLeftItemFormat = array(
    "text" => null,
    "href" => null,
    "html" => null,
    "class" => null,
    "subItems" => array(),
    "position" => "top"
  );

  public static $notifyItem = array(
    "contents" => null,
    "href" => null,
    "icon" => null,
    "iconURL" => null
  );

  /**
   * Add an item to the Top Panel
   * @param string $id   Item ID
   * @param array  $info Item information
   */
  public static function addTopItem($id, $info){
    $info = array_replace_recursive(self::$panelTopItemFormat, $info);
    $loc = $info["position"];

    if($loc === "right"){
      array_reverse($info);
    }

    /**
     * Check if the item is already registered
     * If the item is already registered, then
     * replace it. Else, register new one
     */
    if(isset(self::$topItems[$loc][$id])){
      $merged = array_merge(self::$topItems[$loc][$id], $info);
      self::$topItems[$loc][$id] = $merged;
    }else{
      $originalArr = isset(self::$topItems[$loc]) ? self::$topItems[$loc] : array();
      $merged = array_merge($originalArr, array(
        $id => $info
      ));
      self::$topItems[$loc] = $merged;
    }
  }

  /**
   * Remove an item from Top Panel
   * @param  string $id       ID of item to be removed
   * @param  string $position Position of item in Top Panel. Either "left" or "right"
   * @return boolean          Whether item was removed
   */
  public static function removeTopItem($id, $position){
    if(isset(self::$topItems[$position][$id])){
      unset(self::$topItems[$position][$id]);
      return true;
    }
    return false;
  }

  /**
   * Get Top Panel items
   * @param  string $position Items in which position to get
   * @return array            Array of items
   */
  public static function getTopItems($position = "left"){
    $items = self::$topItems;
    if($position === "right"){
      ksort($items["right"]);
    }
    $items[$position] = Hooks::applyFilters("panel.top.{$position}.items", $items[$position]);
    return $items[$position];
  }

  /**
   * Add an item to the Left Panel
   * @param string $id   Item ID
   * @param array  $info Item information
   */
  public static function addLeftItem($id, $info){
    $info = array_replace_recursive(self::$panelLeftItemFormat, $info);
    $loc = $info["position"];

    if($loc === "bottom"){
      array_reverse($info);
    }

    /**
     * Check if the item is already registered
     * If the item is already registered, then
     * replace it. Else, register new one
     */
    if(isset(self::$leftItems[$loc][$id])){
      $merged = array_merge(self::$leftItems[$loc][$id], $info);
      self::$leftItems[$loc][$id] = $merged;
    }else{
      $originalArr = isset(self::$leftItems[$loc]) ? self::$leftItems[$loc] : array();
      $merged = array_merge($originalArr, array(
        $id => $info
      ));
      self::$leftItems[$loc] = $merged;
    }
  }

  /**
   * Remove an item from Left Panel
   * @param  string $id       ID of item to be removed
   * @param  string $position Position of item in Left Panel. Either "top" or "bottom"
   * @return boolean          Whether item was removed
   */
  public static function removeLeftItem($id, $position){
    if(isset(self::$leftItems[$position][$id])){
      unset(self::$leftItems[$position][$id]);
      return true;
    }
    return false;
  }

  /**
   * Get Left Panel items
   * @param  string $position Items in which position to get
   * @return array            Array of items
   */
  public static function getLeftItems($position = "left"){
    $items = self::$leftItems;
    if($position === "right"){
      ksort($items["right"]);
    }
    $items[$position] = Hooks::applyFilters("panel.left.{$position}.items", $items[$position]);
    return $items[$position];
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
