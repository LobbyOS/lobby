<?php
$q = H::input("question_id");

if($q != null && isset($_POST['options']) && is_array($_POST['options'])){
  $questions = json_decode($this->get("/src/data/questions.json"), true);
  
  $options = array_flip($_POST['options']);
  list($q_parent, $q_child) = explode("-", $q);
  
  if(isset($questions[$q_parent][$q_child])){
    $item = $questions[$q_parent][$q_child];

    shuffle($item['options']);
    foreach($item['options'] as $option){
      if(isset($options[$option])){
        if(hash("md5", $option) == $item['answer']){
          $correct = $option;
        }else{
          $wrong = $option;
        }
      }
    }
    $luck = rand(2, 11);
    if($luck % 2 == 0){
      // Lucky guy, give the right answer
      echo $correct;
    }else{
      // Unlucky, sorry
      echo $wrong;
    }
  }
}

