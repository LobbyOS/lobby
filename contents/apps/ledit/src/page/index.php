<?php
/**
 * We obtain the save name from page-index.php
 */
if(isset($id)){
  $this->setTitle($id);
}
$this->addStyle("main.css");

$this->addScript("date.js");
$this->addScript("main.js");
?>
<script src="<?php echo APP_SRC;?>/src/lib/tinymce/tinymce.min.js"></script>
<div class="contents">
  <ul class="tabs">
    <li class="tab active"><a href="#editor-wrapper">Editor</a></li>
    <li class="tab active"><a href="#saves">Saves</a></li>
  </ul>
  <div id="editor-wrapper">
    <?php
    if(isset($_GET['id'])){
      $id = urldecode(htmlspecialchars_decode($_GET['id']));
      $appData = getData($id, true);
      $content = $appData['value'];
      $created = $appData['created'];
      $updated = $appData['updated'];
    
      /**
       * Show error if a save of the ID is not present
       */
      if($content === ""){
        ser("No Such Save Found");
      }
    }
    ?>
    <div>
      <?php
      if(isset($content)){
      ?>
        <br/>
        <input type="hidden" id="saveName" value="<?php echo $id;?>" />
        <a class="btn" id="save">Update</a>
        <a class="btn red" id="remove">Remove</a>
        <div style="margin: 10px;">Created On <b><?php echo $created;?></b></div>
        <div style="margin: 10px;">Last Updated On <b><?php echo $updated;?></b></div>
      <?php
      }else{
      ?>
        <input id="saveName" type="text" placeholder="The Save Name. Default: today's date" size="35" />
      <?php
      }
      ?>
    </div>
    <div>
      <textarea id="editor" placeholder="Write Something...."><?php
        if(isset($content)){
          echo $content;
        }
      ?></textarea>
    </div><br/>
    <?php
    if(!isset($content)){
    ?>
    <a class="btn" id="save">Save</a>
    <?php
    }
    ?>
    <div id="error"></div>
  </div>
  <div id="saves">
    <?php include(APP_DIR . "/src/ajax/saves.php");?>
  </div>
</div>
