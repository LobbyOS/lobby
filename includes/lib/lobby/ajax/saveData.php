<?php
require "../../../../load.php";

$app = Request::postParam('appID');
$key = Request::postParam('key');
$val = Request::postParam('value');

if($app !== null && $key !== null && $val !== null && CSRF::check()){
  $App = new Lobby\Apps($app);
  if(!$App->exists)
    die("bad");

  var_dump($key);
  if(!$App->getInstance()->saveData($key, $val))
    die("bad");
}else{
  echo "fieldsMissing";
}
?>
