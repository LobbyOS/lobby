<?php
if(isset($_POST['count'])){
  echo $this->friendsCount();
}else if(isset($_POST['get']) && isset($_POST['start'])){
  $this->friends($_POST['start']);
}
