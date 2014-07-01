<?
class ledit extends AppProgram{
	
	public function page($page){
		if($page == "/"){
			return $this->indexPage();
		}
	}
	
	public function indexPage(){
		$this->addStyle("main.css");
		$this->addScript("main.js");
		ob_start();
			include APP_DIR . "/page-index.php";
		$html = ob_get_clean();
		
		/* We obtain the save name from page-index.php */
		if(isset($id)){
			$this->setTitle($id);
		}
		
		return $html;
	}
}
?>