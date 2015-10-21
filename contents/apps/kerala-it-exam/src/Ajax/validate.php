<?php
if(isset($_POST['answers'])){
  /**
   * The Default Values
   */
  $class = $_SESSION['kerala-it-exam-class'];
  $questions = $this->questions($class);
  $marks = json_decode($this->get("/src/Data/mark.json"), true);
  $awarded = array();
  $total_mark = $marks['total'];

  /**
   * The User Data
   */
  $answers = $_POST['answers'];
  $mark = 0;

  /**
   * We don't do any checking, because I don't think the user will try to crack
   * the software. Why would he ? Also, because I'm lazy
   */
  $short = $answers['short'];
  $multiple = $answers['multiple'];
  $note = $answers['note'];
  
  /**
   * First, Short Answers
   */
  foreach($short as $qID => $answer){
    /**
     * The right answer is hashed with MD5
     */
    $right_answer = strtolower($questions['short'][$qID]['answer']); // MD5 hash in lower letters

    if($right_answer == hash("md5", $answer)){
      $mark = $mark + $marks['short'];
    }
  }
  $awarded['short'] = $mark;
  
  /**
   * Multiple Answers
   * This has two answers
   */
  foreach($multiple as $qID => $multiple_answers){
    $wrong = false;
    $right_answers = $questions['multiple'][$qID]['answer'];
    
    foreach($multiple_answers as $answer){
      if(array_search(strtoupper(hash("md5", $answer)), $right_answers) === false){
        $wrong = true;
      }
    }
    /**
     * Only give score if the 2 answers are not wrong
     */
    if($wrong === false){
      $mark = $mark + $marks['multiple'];
    }
  }
  $awarded['multiple'] = abs($mark - $awarded['short']);
  
  /**
   * Short Note
   * This has 4 answers
   */
  foreach($note as $qID => $note_answers){
    $wrong = false;
    foreach($note_answers as $subQID => $answer){
      $right_answer = strtolower($questions['note'][$qID]['answer'][$subQID]);
      
      if(hash("md5", $answer) != $right_answer){
        $wrong = true;
      }
    }
    /**
     * Only give score if all answers are not wrong
     */
    if($wrong === false){
      $mark = $mark + $marks['note'];
    }
  }
  $awarded['note'] = abs($mark - ($awarded['short'] + $awarded['multiple']));
  
  /**
   * Add Full Practical marks
   */
  $mark = $mark + ($marks['practical'] * 4);
  /**
   * Add CE mark and Record Book Mark
   */
  $mark = $mark + $marks['ce'] + $marks['record_book'];
  
  $output = array(
    "score" => $mark,
    "analysis" => "<table style='margin: 20px auto;width: 500px;'><tbody><tr>
      <td>Very Short Answer</td>
      <td>{$awarded['short']}</td>
    </tr>
    <tr>
      <td>Multiple Choice</td>
      <td>{$awarded['multiple']}</td>
    </tr>
    <tr>
      <td>Short Note</td>
      <td>{$awarded['note']}</td>
    </tr>
    <tr>
      <td>Practical</td>
      <td>". ($marks['practical'] * 4) ."</td>
    </tr>
    <tr>
      <td>CE</td>
      <td>". $marks['ce'] ."</td>
    </tr>
    <tr>
      <td>Record Book</td>
      <td>". $marks['record_book'] ."</td>
    </tr>
    </tbody></table>"
  );
  /**
   * Finall print the total score
   */
  echo json_encode($output);
}
