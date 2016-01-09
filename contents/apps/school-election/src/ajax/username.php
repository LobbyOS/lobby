<?php
ini_set("display_errors", "on");
$log = file_get_contents("/var/log/squid/access.log");
$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;
if($ip !== false){
	preg_match_all("/$ip(.*?)election.dev\/\s(.*?)DIRECT/", $log, $m, PREG_SET_ORDER);
	$m = end($m);
	preg_match("/(.*?)\z/", $m[2], $username);
	$username = $username[1];
	echo $username;
}else{
	echo "error";
}
?>
