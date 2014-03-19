<div class="panel top">
 <ul class="left">
 <?
 $items=$LD->top_items;
 foreach($items['left'] as $k=>$v){
 ?>
  <li class="item prnt" id="<?echo strtolower($k);?>">
   <?echo$k;?>
   <?if(count($v)!=0){?>
    <ul>
     <?
     foreach($v as $itemName=>$itemLink){
      echo "<li class='item' id='".strtolower($itemName)."'><a href='$itemLink'>$itemName</a></li>";
     }
     ?>
    </ul>
   <?}?>
  </li>
 <?}?>
 </ul>
 <ul class="right">
 <?
 $items=$LD->top_items;
 ksort($items['right']);
 foreach($items['right'] as $k=>$v){
 ?>
  <li class="item prnt">
   <?echo$k;?>
   <?if(count($v)!=0){?>
    <ul hide class="c_c">
     <?
     foreach($v as $itemName=>$itemLink){
      echo "<li class='item'><a href='$itemLink'>$itemName</a></li>";
     }
     ?>
    </ul>
   <?}?>
  </li>
 <?}?>
 </ul>
</div>
