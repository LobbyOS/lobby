<?php
/* Contains functions that provides additional functionality. */

class Helpers {
	public static function link($url = "", $text = "") {
		$url = self::URL($url);
		return '<a href="'.$url.'">'.$text.'</a>';
	}
	
	public static function URL($path = ""){
		$path 	= substr($path, 0, 1) == "/" ? substr($path, 1) : $path;
		$parts 	= parse_url($path);
		$url	= $path;
		if($path == ""){
			/* If no path, give the current page URL */
			$pageURL = 'http';
			if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
				$pageURL .= "s";
			}
			$pageURL .= "://";
			if($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			}else{
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			$url = $pageURL;
		}elseif( !preg_match("/http/", $path) || $parts['host'] != $GLOBALS['LC']->cleanHost ){
			$url = L_HOST . "/$path";
		}
		return $url;
	}
	
	public static function curPage(){
		$parts = explode("/", $_SERVER['SCRIPT_FILENAME']);
		return $parts[ count($parts)-1 ];
	}
}
?>