<?php
/**
 * Makes the Top Panel
 */
namespace Lobby\UI;

class Panel {

  public static $top_items = array(
    "left" => array(),
    "right" => array()
  );
  
  private static $panel_item_format = array(
    "text" => "",
    "href" => "",
    "subItems" => array(),
    "position" => "left"
  );
  
  public static function addTopItem($name, $array){
    $array = array_replace_recursive(self::$panel_item_format, $array);
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
    if(isset(self::$top_items[$loc][$name])){
      $merged = array_merge(self::$top_items[$loc][$name], $array);
      self::$top_items[$loc][$name] = $merged;
    }else{
      $originalArr = isset(self::$top_items[$loc]) ? self::$top_items[$loc] : array();
      $merged = array_merge($originalArr, array(
        $name => $array
      ));
      self::$top_items[$loc] = $merged;
    }
  }
  
  public static function getPanelItems($side = "left"){
    $items = self::$top_items;
    $html = "";
    if($side == "right"){
      ksort($items['right']);
    }
    return $items[$side];
  }

}
