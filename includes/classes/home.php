<?php
class Design extends L{
 	var $top_items = array();
 	public function addTopItem($name, $array){
  		$loc  = $array['position'];
  		if($loc == "right"){
   		array_reverse($array);
  		}
  		
  		/* Check if the item is already registered */
  		if(isset($this->top_items[$loc][$name])){
   		$merged = array_merge($this->top_items[$loc][$name], $array);
   		$this->top_items[$loc][$name] = $merged;
  		}else{
   		$originalArr = isset($this->top_items[$loc]) ? $this->top_items[$loc] : array();
   		$merged 		 = array_merge($originalArr, array(
   			$name => $array
   		));
   		$this->top_items[$loc] = $merged;
  		}
 	}
 	
 	public function panelItems($side = "left"){
 		$items = $this->top_items;
 		$html  = "";
 		if($side == "right"){
 			ksort($items['right']);
 		}
 		
 		foreach($items[$side] as $id => $item){
 			if( !isset($item['subItems']) ){
 				if( !isset($item['text']) && isset($item['html']) ){
 					$html .= $this->makeItem($item['html'], "htmlContent", $id, "prnt");
 				}else{
 					$html .= $this->makeItem($item['text'], $item['href'], $id, "prnt");
 				}
 			}else{
 				$html .= substr($this->makeItem($item['text'], $item['href'], $id, "prnt"), 0, -5);
 					$html .= "<ul>";
 						foreach($item['subItems'] as $itemID => $subItem){
 							$html .= $this->makeItem($subItem['text'], $subItem['href'], $itemID);
 						}
 					$html .= "</ul>";
 				$html .= "</li>";
 			}
 		}
 		echo $html;
   }
 	
 	public function makeItem($text, $href, $id, $extraClass = ""){
 		$html  = '<li class="item ' . $extraClass . '" id="' . $id . '">';
 			if($href == ""){
 				$html .= $text;
 			}else{
 				$html .= $href == "htmlContent" ? $text : '<a href="' . $href . '">' . $text . '</a>';
 			}
 		$html .= '</li>';
 		return $html;
 	}
}
$LD=new Design();
?>