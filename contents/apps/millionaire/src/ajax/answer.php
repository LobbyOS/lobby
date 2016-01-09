<?php
$q = H::input("question_id");
$answer = H::input("answer");

if($q != null && $answer != null){
  $questions = json_decode($this->get("/src/data/questions.json"), true);
  
  list($q_parent, $q_child) = explode("-", $q);
  if(isset($questions[$q_parent][$q_child])){
    $item = $questions[$q_parent][$q_child];
    $hashed_answer = hash("md5", $answer);

    if($hashed_answer == strtolower($item['answer'])){
      $_SESSION['app-millionaire-level'] = $q_parent;
      echo "correct";
    }else{
      foreach($item['options'] as $option){
        if(hash("md5", $option) == $item['answer']){
          $right_answer = $option;
        }
      }
      
      echo json_encode(array(
        "money" => $this->money(),
        "correct" => $right_answer
      ));
    }
  }
}
