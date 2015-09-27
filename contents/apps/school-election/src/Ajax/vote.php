<?php
include \Lobby\FS::loc("/src/Inc/load.php");

if(isset($_POST['vote']) && isset($_POST['candidates']) && isset($_POST['voterID'])){
	
  if(count($_POST['candidates']) > 3 && $_SESSION['election-validated'] == "true"){
    $can = $_POST['candidates'];
		$voterID = $_POST['voterID']; // Gets as "10d29". We then uppercase it

		if(!$ELEC->didVote($voterID)){	
			/**
       * Tell PHP that this student has voted
       */
			$ELEC->vote($voterID, $can);

			echo "success";
      $_SESSION['election-validated'] = "false";
		}else{
			echo "voted";
		}
	}else{
	 	echo "error";
	}
}else{
	echo "error";
}
?>
