<div class="contents">
 <?
 if(isset($_GET['id'])){
  $id=urldecode(htmlspecialchars_decode($_GET['id']));
  $data=getData("ledit", $id);
  foreach($data as $v){
   $cnt=$v['content'];
   $updated=$v['updated'];
  }
  if($cnt==""){
   ser("No Such Save Found");
  }
 }
 ?>
 <div id="saves">
  <?include(CUR_APP."ajax/saves.php");?>
 </div>
 <div id="editor">
  <div>
   <?if(isset($cnt)){?>
    <div style="font-size:18px;margin-bottom:5px;font-weight:bold;"><?echo filt($id);?></div>
    <div style="margin-bottom:5px;">Last Updated On <b><?echo$updated;?></b></div>
   <?}?>
   <textarea id="text" placeholder="Write Something...."><?
    if(isset($cnt)){
     echo $cnt;
    }
    ?></textarea>
  </div>
  <div style="margin-top:5px;">
   <?
   if(isset($cnt)){
   ?>
    <input type="hidden" id="saveName" value="<?echo$id;?>"/>
    <a class="button" id="save">Update</a>
    <a class="button" id="remove">Remove</a>
   <?}else{?>
    <a class="button" id="save">Save</a>
    <input id="saveName" type="text" placeholder="The Save Name. Default : timestamp" size="35"/>
   <?}?>
  </div>
  <div id="saved">Saved Successfully</div>
  <div id="error"></div>
 </div>
</div>
