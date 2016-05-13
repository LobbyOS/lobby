<?php
set_time_limit(-1);
ini_set('memory_limit', '2048M');

if(isset($argv[1])){
  $cmd = base64_decode($argv[1]);
  
  exec($cmd);
}
