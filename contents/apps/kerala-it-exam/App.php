<?php
namespace Lobby\App;

/**
 * The Kerala IT Exam Class
 */
class kerala_it_exam extends \Lobby\App {
  
  public function page($p){
    return "auto";
  }
  
  /**
   * Fetch the questions array
   */
  public function questions($class){
    return json_decode($this->get("/src/Data/questions.{$class}.json"), true);
  }
  
  /**
   * Pick Random Questions and insert into session
   */
  public function generateQuestions($class = "8"){
    /**
     * Make the question array
     */
    $allQuestions = $this->questions($class);
    
    $end_qs = array(); // Questions picked out for the student
    
    /**
     * 8 Short Answer Questions
     */
    $questions = $allQuestions['short'];
    $count = count($questions) - 1; // Total questions
    
    $random_numbers = range(0, $count);
    shuffle($random_numbers);

    for($i = 0;$i < 8;$i ++){
      $index = $random_numbers[$i]; // Pick a random question
      $end_qs['short'][$index] = $questions[$index];
    }
    
    /**
     * 4 Multiple Choice Questions
     */
    $questions = $allQuestions['multiple'];
    $count = count($questions) - 1; // Total questions
    
    $random_numbers = range(0, $count);
    shuffle($random_numbers);
    
    for($i = 0;$i < 4;$i ++){
      $index = $random_numbers[$i]; // Pick a random question
      $end_qs['multiple'][$index] = $questions[$index];
    }
    
    /**
     * 2 Short Note Questions
     */
    $questions = $allQuestions['note'];
    $count = count($questions) - 1; // Total questions
    
    $random_numbers = range(0, $count);
    shuffle($random_numbers);
    
    for($i = 0;$i < 2;$i ++){
      $index = $random_numbers[$i]; // Pick a random question
      $end_qs['note'][$index] = $questions[$index];
    }
    /**
     * 8 (4 * 2 choices) Practical Questions
     */
    $questions = $allQuestions['practical'];
    $count = count($questions) - 1; // Total questions
    
    $random_numbers = range(0, $count);
    shuffle($random_numbers);
    
    for($i = 0;$i < 8;$i ++){
      $index = $random_numbers[$i]; // Pick a random question
      $questions[$index]['question'] = $this->filterQuestion($questions[$index]['question']);
      $end_qs['practical'][$index] = $questions[$index];
    }
    $end_qs['practical'] = array_chunk($end_qs['practical'], 2);
    $_SESSION['kerala-it-exam-qs'] = $end_qs; // Add questions array to session
  }
  
  /**
   * Make the sidebar
   */
  public function sidebar($type){
    if($type == "note"){
      echo "<div class='toggleGroupContainer'>";
        for($i = 1;$i < 3;$i++){
          echo "<a class='button red toggleGroup' data-id='". $i ."'>Group $i</a>";
        }
      echo "</div>";
      echo "<div class='toggleGroupQuestions'>";
        for($i = 1;$i < 5;$i++){
          echo "<a class='button blue toggleQuestion' data-type='{$type}' data-id='". ($i - 1) ."'>Question $i</a>";
        }
      echo "</div>";
    }else{
      $lastQuestionNo = $type == "short" ? 8 : 4;
      for($i = 1;$i < ($lastQuestionNo + 1);$i++){
        echo "<a class='button blue toggleQuestion' data-type='{$type}' data-id='". ($i - 1) ."'>Question $i</a>";
      }
    }
  }
  
  /**
   * Pre function of Printing HTML markup of questions
   * PS : I don't know how to explain this. I'm crazy
   */
  public function outputQuestions($questions, $type){
    if($type == "note"){
      function groupArray($qid, $arr){
        $newArr = array();
        /*
         * Actually for the 4 question with diff options, the question is same
         */
        foreach($arr['options'] as $optID => $options){
          $newArr["$qid][$optID"] = array(
            "question" => $arr['question'],
            "options" => $options
          );
        }
        return $newArr;
      }
      
      $qKeys = array_keys($questions[$type]);
      $group1 = groupArray($qKeys[0], $questions[$type][$qKeys[0]]);
      $group2 = groupArray($qKeys[1], $questions[$type][$qKeys[1]]);
      
      // Print out questions of two groups
      echo "<div class='group' id='1'>";
        $this->printQuestions($group1, $type);
      echo "</div>";
      echo "<div class='group' id='2'>";
        $this->printQuestions($group2, $type);
      echo "</div>";
    }else{
      $qArr = $questions[$type];
      $this->printQuestions($qArr, $type);
    }
  }
  
  /**
   * Print HTML markup of a question
   */
  public function printQuestions($qArr, $type){
    $i = 0;
    $multipleI = 0;
    
    foreach($qArr as $qid => $question){
      echo '<div class="questionArea" id="'. $i .'">';
        echo '<div class="question">'. $this->filterQuestion($question['question']) .'</div>';
        echo '<div class="options">';
          foreach($question['options'] as $option){
            if($type == "multiple"){
              $name = "answers[{$type}][{$qid}][{$multipleI}]";
              $multipleI++;
            }else{
              $name = "answers[{$type}][{$qid}]";
            }
            echo "<label class='option'><input type='checkbox' name='{$name}' value='{$option}' /><span>{$option}</span></label>";
          }
        echo '</div>';
      echo '</div>';
      $i++;
    }
  }
  
  /**
   * Filter Question
   */
  public function filterQuestion($str){
    $new_str = preg_replace("/\[img\]\((.*?)\)/", "<img src='". APP_SRC . "/src/Data/image/$1" ."' />", $str);
    $new_str = str_replace("\n", "<br/>", $new_str);
    return $new_str;
  }

}
?>
