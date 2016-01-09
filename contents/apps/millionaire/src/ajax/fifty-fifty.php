<?php
$q = H::input("question_id");

if($q != null){
  $questions = json_decode($this->get("/src/data/questions.json"), true);
  
  list($q_parent, $q_child) = explode("-", $q);
  if(isset($questions[$q_parent][$q_child])){
    $item = $questions[$q_parent][$q_child];

    $wrong = array();
    shuffle($item['options']);
    foreach($item['options'] as $option){
      if(hash("md5", $option) != $item['answer'] && count($wrong) != 2){
        $wrong[] = $option;
      }
    }
    echo json_encode($wrong);
  }
}

