<?php
class siteDASHcompressor extends AppProgram{
    public function page($page){
		if( $page == "/" ){
			$this->setTitle("Site Compressor");
			ob_start();
				include APP_DIR . "/pages/index.php";
			$html = ob_get_clean();
			
			return $html;
		}elseif( $page == "/site" ){
			$this->setTitle("Compress A Site");
			
			$this->addStyle("cdn/main.css");
			$this->addStyle("cdn/scrollbar.css");
			$this->addScript("cdn/main.js");
			$this->addScript("cdn/scrollbar.js");
			
			return $this->inc("/pages/site.php");
		}elseif( $page == "/html" || $page == "/css" || $page == "/js" ){
			$this->setTitle("Compress ". strtoupper(substr($page, 1)));
			return $this->inc("/pages/$page.php");
		}
    }
}
?>