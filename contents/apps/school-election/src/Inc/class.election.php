<?php
class Election {

 	/**
   * The last possible value of Roll Number in all the classes. Roll Number with the value given is the last person that can vote. Example : If it's 70, then in all the classess, Roll Numbers above 70 can't vote.
   */
 	
 	public function __construct($config){
    $this->config = $config;
    return true;
 	}
 	
 	public function showCandidates(){
    /**
     * Boys Section
     */
 		echo "<div class='boys'>";
 			echo "<h2>Boys</h2>";
 			$this->candidates(unserialize(getData("male_candidates")));
 		echo "</div>";
 		
 		/**
     * Girls Section
     */
 		echo "<div class='girls'>";
 			echo "<h2>Girls</h2>";
 			$this->candidates(unserialize(getData('female_candidates')));
 		echo "</div>";
 	}
 	
 	public function candidates($data){
 		if(count($data) == $this->config['female_candidates']){
      foreach($data as $id => $candidate){
 			  echo "<div class='candidate'>";
 				  echo "<label>";
            echo "<input type='checkbox' name='candidates[]' value='$id' />$candidate";
          echo "</label>";
   			echo "</div>";
 		  }
    }else{
      echo "No Candidates Found";
    }
 	}
 	
 	/**
   * Check if the student has already voted
   */
 	public function didVote($id){
 		$id = strtoupper($id);
 		
    $votes = unserialize(getData("election_votes"));
    if(!is_array($votes)){
      $votes = array();
    }
      
		return isset($votes[$id]) === false ? false : true;
 	}

 	
 	/**
   * This is the updatal of candidate vote
   */
 	public function vote($voterID, $candidates){
 		if($voterID != ""){
 			$voterID = strtoupper($voterID); // I have no idea why I chose upper case characters
      $votes = unserialize(getData("election_votes"));
      
      if(!is_array($votes)){
        $votes = array();
      }
      $votes[$voterID] = $candidates;
      saveData("election_votes", serialize($votes));
 		}
 	}
 	
 	/**
   * See if Election Is Already Started
   */
 	public function isElection($type = ""){
 		if($type == ""){
 			$male_candidates = unserialize(getData("male_candidates"));
      $female_candidates = unserialize(getData("female_candidates"));
      $count = count($male_candidates) + count($female_candidates);
 		}elseif($type == "male"){
      $male_candidates = unserialize(getData("male_candidates"));
      $count = count($male_candidates);
 		}elseif($type == "female"){
      $female_candidates = unserialize(getData("female_candidates"));
      $count = count($female_candidates);
 		}

 		if($type == ""){
 			return $count != $this->config['total_candidates'] ? false:true;
 		}else{
 			return $count != $this->config["{$type}_candidates"] ? false:true;
 		}
 	}
 	
 	/* Make changes to the live election page */
 	public function liveChange($type = ""){
 		$codes = array(
 			"reload" => "window.location = window.location;",
 			"reset"  => "1"
 		);
 		if( isset($codes[$type]) ){
 			saveData("election_ajax_script", $codes[$type]);
 		}else{
 			return false;
 		}
 	}
  
  /**
   * Get Votes
   */
  public function count($candidateNames){
    $votes = unserialize(getData("election_votes"));
    $votes = is_array($votes) ? $votes : array();
    
    $candidates = array_flip($candidateNames);
    $candidates = array_fill_keys(array_keys($candidates), 0);
    
    foreach($votes as $canID => $vote){
      foreach($vote as $canID){
        if(isset($candidateNames[$canID])){
          $candidates[$candidateNames[$canID]] = $candidates[$candidateNames[$canID]] + 1;
        }
      }
    }
    return $candidates;
  }
 	
 	/**
   * Clear Data
   */
 	public function clear(){
 		removeData("male_candidates");
    removeData("female_candidates");
    removeData("student_passwords");
    removeData("election_votes");
 	}
}
?>
