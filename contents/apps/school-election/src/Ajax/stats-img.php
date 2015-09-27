<?php
include \Lobby\FS::loc("/src/Inc/load.php");
include \Lobby\FS::loc("/src/Inc/graph.php");

$data = array();

// Set config directives
$cfg['title'] = 'Election Results';
$cfg['width'] = 600;
$cfg['height'] = 300;
$cfg['value-font-size'] = 4;
$cfg['key-font-size'] = 6;

$candidateNames = unserialize(getData("female_candidates")) + unserialize(getData("male_candidates"));
$votes = $ELEC->count($candidateNames);

foreach($votes as $name => $votes){
  $data[$name] = $votes;
}

$graph = new phpMyGraph();

ob_start();
  $graph->parseVerticalColumnGraph($data, $cfg);
$img = ob_get_clean();
echo base64_encode($img);
?> 
