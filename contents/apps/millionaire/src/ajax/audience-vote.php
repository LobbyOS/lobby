<?php
$q = H::input("question_id");

if($q != null){
  $questions = json_decode($this->get("/src/data/questions.json"), true);
  
  list($q_parent, $q_child) = explode("-", $q);
  if(isset($questions[$q_parent][$q_child])){
    $item = $questions[$q_parent][$q_child];
    
    $correct_answer = rand(50, 65);
    $total = 100 - $correct_answer;
    
    $percentage = array();
    shuffle($item['options']);
    
    foreach($item['options'] as $option){
      if(hash("md5", $option) == $item['answer']){
        $percentage[$option] = $correct_answer;
      }else{
        $p = rand(0, $total);
        $percentage[$option] = $p;
        $total = $total - $p;
      }
    }
    echo json_encode($percentage);
  }
}
