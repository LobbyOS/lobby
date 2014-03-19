<?
class Design extends L{
 var $top_items = array();
 function addTopItem($name, $array, $loc){
  if($loc=="right"){
   array_reverse($array);
  }
  if(isset($this->top_items[$loc][$name])){
   $merged=array_merge($this->top_items[$loc][$name], $array);
   $this->top_items[$loc][$name]=$merged;
  }else{
   $orr=isset($this->top_items[$loc]) ? $this->top_items[$loc]:array();
   $merged=array_merge($orr, array($name => $array));
   $this->top_items[$loc]=$merged;
  }
 }
}
$LD=new Design();
?>
