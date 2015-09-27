<div class='contents'>
  <h1>ChatKin</h1>
  <p>Chat with all your friends at once.</p>
  <center clear>
    <a href='<?php echo APP_URL . "/chat";?>' class='button green'>Go To Chat</a>
    <a href='<?php echo APP_URL . "/accounts";?>' class='button red'>Connected Accounts</a>
  </center>
  <div clear>
    <?php
    foreach($this->available_networks as $network => $null){
      echo '<a href="'. APP_URL .'/connect?network='. $network .'" class="button blue">Connect '. ucfirst($network) .'</a>';
    }
    ?>
  </div>
</div>
