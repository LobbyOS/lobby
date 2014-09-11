<?php
if( isset($_POST['length']) ){
	$lHTML		= "";
	$letters	= array("a", "g", "y");
	$chars		= 'abcdefghijklmnopqrstuvwxyz';
	$size		= strlen($chars);
	/*for($i = 0;$i < $_POST['length'];$i++){
		$str		 = $chars[rand(0, $size-1)];
		$lHTML		.= "<a href='' class='letter'>$str</a>";
		$letters[]	 = $str;
	}*/
	print_r($letters);
	
	/* Find possible words */
	$words 		= file_get_contents(APP_DIR . "/wordlist.txt");
	$words 		= explode("\n", $words);
	$pWords		= array(); // Possible words
	$already	= array();
	
	function mWord($index, $letter){
		global $pWords, $words, $letters;
		$used = array($index => "c");
		foreach( $letters as $key => $single ){
			if( !isset($used[$key]) ){
				$cWord = $letter . $single;
				if( array_search($cWord, $words) !== false ){
					echo $cWord;
					ob_flush();flush();
					$pWords[strlen($cWord)] = $cWord;
				}
				mWord(false, $cWord);
			}
		}
	}
	
	function findWord($index){
		global $letters, $words, $pWords, $already;
		if( !isset($already[$index]) && isset($letters[$index]) ){
			$letter = $letters[$index];
			mWord($index, $letter, $letters);
			$already[$index] = 1;
			findWord($index + 1);
		}
	}
	findWord(0);
	
	/* Wrap it up */
	$arr = array(
		"str" 	=> $lHTML,
		"words" => $pWords
	);
	echo json_encode($arr);
}
?>