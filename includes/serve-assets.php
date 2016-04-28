<?php
require_once "../load.php";

Assets::$preProcess = function($data, $type){
  $to_replace = array(
    "<?L_URL?>" => L_URL,
    "<?THEME_URL?>" => THEME_URL
  );
  if(isset($_GET['APP_URL'])){
    $to_replace["<?APP_URL?>"] = htmlspecialchars(urldecode($_GET['APP_URL']));
    $to_replace["<?APP_SRC?>"] = htmlspecialchars(urldecode($_GET['APP_SRC']));
  }
  foreach($to_replace as $from => $to){
    $data = str_replace($from, $to, $data);
  }
  return $data;
};
Assets::serve();
?>
