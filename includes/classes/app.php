<?php
class AppProgram {
	private $LC;
	public $dir, $url, $id;
	
	public function setTheVars($LC, $array){
		$this->LC   = $LC;
		$this->id   = $array['id'];
		$this->name = $array['name'];
		$this->URL  = $array['URL'];
	}
	
	public function addStyle($fileName){
		$this->LC->addStyle("{$this->id}-{$fileName}", "{$this->URL}/$fileName");
	}
	
	public function addScript($fileName){
		$this->LC->addScript("{$this->id}-{$fileName}", "{$this->URL}/$fileName");
	}
	
	public function setTitle($title){
		$this->LC->setTitle("$title | {$this->name}");
	}
	
	public static function URL($path, $source = false){
		return $source === false ? Helpers::URL(APP_URL. $path) : Helpers::URL(APP_SOURCE. $path) ;
	}
	
	/* Include a page from the app's source */
	public function inc ($path, $vars = array()){
		/* Define variables for the file */
		$LC = $this->LC;
		if(count($vars) != 0){
			extract($vars);
		}
		
		/* Get the content of the file in a variable */
		ob_start();
			include APP_DIR . $path;
		$html = ob_get_clean();
		return $html;
	}
}
?>