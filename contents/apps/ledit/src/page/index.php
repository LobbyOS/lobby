<div class="contents">
  <?php
  if(isset($_GET['id'])){
    $id = urldecode(htmlspecialchars_decode($_GET['id']));
    $appData = getData($id, true);
    $content = $appData['content'];
    $updated = $appData['updated'];

    /* Show error if a save of the ID is not present */
    if($content == ""){
      ser("No Such Save Found");
    }
  }
  ?>
  <div class="saves-outer">
    <div id="saves">
      <?php include(APP_DIR . "/src/ajax/saves.php");?>
    </div>
  </div>
  <div id="editor">
    <div>
      <?php if(isset($content)){ ?>
        <h2 style="margin: 0 0 10px;white-space: pre;text-overflow: ellipsis;overflow: hidden;"><?php echo filt($id);?></h2>
        <div style="margin-bottom:5px;">Last Updated On <b><?php echo $updated;?></b></div>
      <?php }?>
      <textarea id="text" placeholder="Write Something...."><?php
        if(isset($content)){
          echo $content;
        }
      ?></textarea>
    </div>
    <div style="margin-top:5px;">
      <?php
      if(isset($content)){
      ?>
        <input type="hidden" id="saveName" value="<?php echo $id;?>" />
        <a class="button" id="save">Update</a>
        <a class="button" id="remove">Remove</a>
      <?php
      }else{
      ?>
        <a class="button" id="save">Save</a>
        <input id="saveName" type="text" placeholder="The Save Name. Default : timestamp" size="35" />
      <?php
      }
      ?>
    </div>
    <div id="saved">Saved Successfully</div>
    <div id="error"></div>
  </div>
</div>
