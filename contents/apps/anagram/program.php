<?php
class anagram extends AppProgram{
	public function page($p){
		if( $p == "/" ){
			$this->addStyle("/game.css");
			$this->addScript("/game.js");
			return $this->inc("/page-index.php");
		}
	}
}
?>