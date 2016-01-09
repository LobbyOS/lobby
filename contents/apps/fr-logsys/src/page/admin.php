<?php
require_once APP_DIR . "/src/inc/partial/layout.php";
?>
<div class='contentLoader'>
  <h1>Home</h1>
  <?php
  if($this->set){
    $this->setInfo();
  ?>
    <div class='stat'>
      <a href='<?php echo APP_URL;?>/admin/users'>
        <div class='count'><?php echo $this->info['users'];?></div>
        <span>Total Users</span>
      </a>
    </div>
    <div class='stat'>
      <a href='<?php echo APP_URL;?>/admin/tokens'>
        <div class='count'><?php echo $this->info['verify_tokens'];?></div>
        <span>Unused Tokens</span>
      </a>
    </div>
    <div class='stat'>
      <a href='<?php echo APP_URL;?>/admin/stats'>
        <div class='count'><?php echo $this->registeredInAMonth();?></div>
        <span>New User(s) In Last Month</span>
      </a>
    </div>
    <div style='background: white;padding: 20px 15px;margin: 40px 0px;width: 450px;'>logSys is running because of support and feedback from you. If you found logSys helpful, please consider a <a target='_blank' class='button red' href='http://subinsb.com/donate?utm_source=lobby_logsys.admin
'>Donation</a>.<br/><a target='_blank' href='http://subinsb.com/?utm_source=lobby_logsys.admin'><img src='<?php echo APP_SRC;?>/src/Image/blog_logo.png' width='100%'  /></a>
    <ul>
      <li><a href='http://subinsb.com/php-logsys?utm_source=lobby_logsys.admin' target='_blank'>logSys Documentation</a></li>
      <li><a href='http://github.com/subins2000/logSys' target='_blank'>GitHub Repository</a></li>
    </ul>
  </div>
  <?php
  }else{
  ?>
    <a href='<?php echo APP_URL;?>/admin/config' class='button red'>Setup logSys Admin</a>
  <?php
  }
  ?>
</div>
<?php require_once APP_DIR . "/src/inc/partial/layout_footer.php";?>
