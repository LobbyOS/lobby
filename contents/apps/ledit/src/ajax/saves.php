<?php
$saves = $this->getData("", "ledit");
if( !$saves ){
  echo '<div class="saveItem">You haven\'t saved anything.</div>';
}else{
  function cmp($a, $b) {
    return strtotime($b["updated"]) - strtotime($a["updated"]);
  }
  usort($saves, "cmp");
  foreach($saves as $save){
    $url = $this->url . "?id=" . urlencode($save['name']);
?>
    <a href="<?php echo $url;?>">
      <div class="saveItem">
        <div class="title"><?php echo $save['name'];?></div>
        <div class="updated"><?php echo $save['updated'];?></div>
      </div>
    </a>
<?php
  }
}
?>
