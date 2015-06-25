<?php
/**
 * Makes the Top Panel
 */
namespace Lobby;

class Panel extends \Lobby {

  public static $top_items = array(
    "left" => array(),
    "right" => array()
  );
  
  public static function addTopItem($name, $array){
    $loc = $array['position'];
    if($loc == "right"){
      array_reverse($array);
    }

    /* Check if the item is already registered */
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
  
  public static function panelItems($side = "left"){
    $items = self::$top_items;
    $html = "";
    if($side == "right"){
      ksort($items['right']);
    }
    
    foreach($items[$side] as $id => $item){
      if( !isset($item['subItems']) ){
        if( !isset($item['text']) && isset($item['html']) ){
          $html .= self::makeItem($item['html'], "htmlContent", $id, "prnt");
        }else{
          $html .= self::makeItem($item['text'], $item['href'], $id, "prnt");
        }
     }else{
        $html .= substr(self::makeItem($item['text'], $item['href'], $id, "prnt"), 0, -5);
          $html .= "<ul>";
          foreach($item['subItems'] as $itemID => $subItem){
            $html .= self::makeItem($subItem['text'], $subItem['href'], $itemID);
          }
          $html .= "</ul>";
        $html .= "</li>";
      }
    }
    echo $html;
  }
  
  public static function makeItem($text, $href, $id, $extraClass = ""){
    $html = '<li class="item ' . $extraClass . '" id="' . $id . '">';
     if($href == ""){
      $html .= $text;
     }else{
      $html .= $href == "htmlContent" ? $text : self::l($href, $text);
     }
    $html .= '</li>';
    return $html;
  }
}
?>
