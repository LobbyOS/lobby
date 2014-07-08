<?
class L {
 	public $debug, $root, $host, $title = "";
 	var $js = array();
 	var $css = array();
 
 	function __construct(){
  		register_shutdown_function( array($this, "fatalErrorHandler") );
  		$this->root = L_ROOT;
  		$docRoot 	= substr($_SERVER['DOCUMENT_ROOT'], -1)=="/" ? substr_replace($_SERVER['DOCUMENT_ROOT'],"",-1) : $_SERVER['DOCUMENT_ROOT'];
  		$host	  	   = str_replace($docRoot, $_SERVER['HTTP_HOST'], L_ROOT);
  		$this->host = "http://$host";
 	}
 
 	public function debug($value = false){
  		if($value){
   		ini_set("display_errors","on");
   		$this->debug = $value;
  		}
 	}
 
 	public function addScript($name, $url){
  		$this->js[$name]  = $url;
 	}
 
 	public function addStyle($name, $url){
  		$this->css[$name] = $url;
 	}
 
 	public function head($title=""){
   	if($title!=""){
   		$this->setTitle($title);
   	}
  	
  		/* JS */
  		if(count($this->js)!=0){
   		echo "<script async='async' src='" . L_HOST . "/includes/serve.php?file=" . implode(",", $this->js) . "'></script>";
  		}
  		/* CSS */
  		if(count($this->css)!=0){
  			echo "<link async='async' href='" . L_HOST . "/includes/serve.php?file=" . implode(",", $this->css) . "' rel='stylesheet'/>";
  		}
  		
  		/* Title */
  		echo "<title>" . $this->title . "</title>";
 	}
 
 	/* Set the Page title */
 	public function setTitle($title = ""){
 		if($title != ""){
 			$this->title = $title;
 			if($this->title == ""){
   			$this->title = "Lobby";
  			}else{
   			$this->title .= " - Lobby";
  			}
 		}
 	}
 	
 	/* A redirect function that support HTTP status code for redirection 
  	* 302 - Moved Temporarily
 	*/
 	public function redirect($url, $status=302){
  		header("Location: $url", true, $status);
  		exit;
  		return true;
 	}
 	
 	/* Generate a random string */
 	public function randStr($length){
  		$str="";
  		$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  		$size=strlen($chars);
  		for($i=0;$i<$length;$i++){
   		$str.=$chars[rand(0, $size-1)];
  		}
  		return $str;
 	}
 	
 	/* Logs Stuff */
 	public function logStatus($msg = ""){
 		if( $msg != "" && $this->debug === true ){
 			$logFile = "{$this->root}/contents/extra/lobby.log";
 			$message = "[" . date("Y-m-d H:i:s") . "] $msg";
 			
 			$fh = fopen($logFile, 'a');
			fwrite($fh, $message."\n");
			fclose($fh);
		}
 	}
 	
 	/* A handler for Fatal Errors occured in PHP */
 	public function fatalErrorHandler(){
  		$error = error_get_last();

  		if( $error !== NULL) {
    		$errType = $error["type"];
    		$errFile = $error["file"];
    		$errLine = $error["line"];
    		$errStr  = $error["message"];
    		
    		$error = "$errType caused by $errFile on line $errLine : $errStr";
    		$this->logStatus($error);
  		}
 	}
 	
 	/* A HTTP Request Function */
 	public function loadURL($url, $params=array(), $type="GET"){
 		$ch = curl_init();
 		if(count($params) != 0){
  			$fields_string = "";
  			foreach($params as $key => $value){
   			$fields_string .= "{$key}={$value}&";
  			}
  			/* Remove Last & char */
  			rtrim($fields_string, '&');
 		}
 		
 		if($type == "GET" && count($params) != 0){
  			/* Append Query String Parameters */
  			$url .= "?{$fields_string}";
 		}
 		
 		/* Start Making cURL request */
 		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 		
 		if($type == "POST" && count($params) != 0){
  			curl_setopt($ch, CURLOPT_POST, count($params));
  			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
 		}
 		/* Give back the response */
 		$output = curl_exec($ch);
 		return $output;
	}
}
$LC = new L();
?>