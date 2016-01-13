<?php
$data = getData("election_ajax_script", true);
if(strtotime($data['updated']) > strtotime("-20 seconds")){
  echo $data['value'];
}
