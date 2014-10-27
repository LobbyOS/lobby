<?php
require_once "../load.php";

$f	= $_GET['file'];
if(preg_match("/\.css/", $f)){
 	header("Content-type: text/css");
 	$css = 1;
}
if(preg_match("/\.js/", $f) && !isset($css)){
 	header("Content-type: application/x-javascript");
 	$js = 1;
}
if(preg_match("/,/", $f)){
 	$files = explode(",",$f);
}else{
 	$files = array($f);
}
$content		= "";
$extraContent 	= "";

/* Loop through files and */
foreach($files as $file){
 	$file = str_replace(L_HOST, "", $file);
 	if($file == "/includes/lib/jquery/jquery-ui.js" || $file == "/includes/lib/jquery/jquery.js"){
  		$extraContent .= file_get_contents(L_ROOT . $file);
 	}else{
  		$content .= file_get_contents(L_ROOT . $file);
 	}
 	if(isset($css)){
  		$to_replace = array(
			"<[host]>" => L_HOST
  		);
  		foreach($to_replace as $from => $to){
			$content = str_replace($from, $to, $content);
  		}
 	}
}
if(isset($js)){
 	$extra	 = "window.lobby={};";
 	$content  = $extra . "lobby.host='".L_HOST."';" . $content;
 	$content  = "$(document).ready(function(){".$content."});";
}
echo $extraContent.$content;
?>