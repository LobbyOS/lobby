<?php
/* Contains functions that provides additional functionality. */

class Helpers {
	public static function link($url = "", $text = "", $extra = "") {
		$url = self::URL($url);
		return '<a href="'.$url.'" '. $extra .'>'.$text.'</a>';
	}
	
	public static function URL($path = ""){
		$orPath	= $path; // The original path
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
			if(!defined("APP_DIR")){
				$url = L_HOST . "/$path";
			}else{
				$url = AppProgram::URL($orPath);
			}
		}
		return $url;
	}
	
	public static function curPage($page = false){
		if($page){
			$parts = explode("/", $_SERVER['REQUEST_URI']);
			return $parts[ count($parts)-1 ];
		}else{
			return $_SERVER['REQUEST_URI'];
		}
	}
}
?>