<div class="contents">
  <?php
  $classes = array(10 => 0, 9 => 0, 8 => 0);
  $class = isset($_GET['class']) ? $_GET['class'] : "";
  
  if(!isset($classes[$class])){
    ser("Invalid Class");
  }else{
    /**
     * Load the questions
     */
    $questions_file_loc = APP_DIR . "/src/Data/questions.$class.json";
    $questions = json_decode($this->get("/src/Data/questions.$class.json"), true);
    $questions = array_merge_recursive(array(
      "short" => array(),
      "multiple" => array(),
      "note" => array(),
      "practical" => array()
    ), $questions);
    
    /**
     * Update Questions and save to file
     */
    if(isset($_POST['short']) && isset($_POST['multiple']) && isset($_POST['note']) && isset($_POST['practical']) && \H::csrf()){
      $questions = array_replace_recursive($questions, array(
        "short" => $_POST['short'],
        "multiple" => $_POST['multiple'],
        "note" => $_POST['note'],
        "practical" => $_POST['practical']
      ));
      foreach($questions['short'] as $i => $arr){
        $questions['short'][$i]['answer'] = $arr['answer'] == "" ? "" : strtoupper(hash("md5", $arr['answer']));
      }
      foreach($questions['multiple'] as $i => $arr){
        $questions['multiple'][$i]['answer'][0] = $arr['answer'][0] == "" ? "" : strtoupper(hash("md5", $arr['answer'][0]));
        $questions['multiple'][$i]['answer'][1] = $arr['answer'][1] == "" ? "" : strtoupper(hash("md5", $arr['answer'][1]));
      }
      foreach($questions['note'] as $i => $arr){
        $questions['note'][$i]['answer'][0] = $arr['answer'][0] == "" ? "" : strtoupper(hash("md5", $arr['answer'][0]));
        $questions['note'][$i]['answer'][1] = $arr['answer'][1] == "" ? "" : strtoupper(hash("md5", $arr['answer'][1]));
        $questions['note'][$i]['answer'][2] = $arr['answer'][2] == "" ? "" : strtoupper(hash("md5", $arr['answer'][2]));
        $questions['note'][$i]['answer'][3] = $arr['answer'][3] == "" ? "" : strtoupper(hash("md5", $arr['answer'][3]));
      }
      file_put_contents($questions_file_loc, json_encode($questions));
      sss("Saved!", "The questions was saved successfully.");
    }
  ?>
    <h1>Manage Questions</h1>
    <p>Note :</p>
    <ul>
      <li>Use single quotes <b>not double quotes ("")</b></li>
      <li>For inserting images into questions, use the following markup : <blockquote>[img](relative_path_in_src/Data/10.png)</blockquote></li>
      <li>Answer values are <b>Case Sensitive</b>. So, answer must be absolutely same as the correct option</li>
    </ul>
    <h2>Short Answer</h2>
    <form method="POST" action="<?php echo \Lobby::u();?>">
      <?php
      foreach($questions['short'] as $id => $questionArr){
      ?>
        <h4>Question # <?php echo $id+1;?></h4>
        <div clear style="margin-left: 20px;">
          <label>
            <div>Question</div>
            <input type='text' name='short[<?php echo $id;?>][question]' value="<?php echo $questionArr['question'];?>"id='question_input' />
          </label>
          <label>
            <div>Options</div>
            <?php
            $hashed = array();
            foreach($questionArr['options'] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='short[<?php echo $id;?>][options][]' value="<?php echo $option;?>" />
            <?php
            }
            ?>
          </label>
          <label>
            <div>Answer</div>
            <input type='text' name='short[<?php echo $id;?>][answer]' value="<?php echo array_search($questionArr['answer'], $hashed);?>"/>
          </label>
        </div>
      <?php
      }
      ?>
      <a id='newShortQuestion' class='button blue'>Add New Short Answer Question</a>
      <h2 clear>Multiple Choice Questions</h2>
      <?php
      foreach($questions['multiple'] as $id => $questionArr){
      ?>
        <h4>Question # <?php echo $id+1;?></h4>
        <div clear style="margin-left: 20px;">
          <label>
            <div>Question</div>
            <textarea type='text' name='multiple<?php echo $id;?>][question]' id='question_input'><?php echo $questionArr['question'];?></textarea>
          </label>
          <label>
            <div>Options</div>
            <?php
            $hashed = array();
            foreach($questionArr['options'] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='multiple[<?php echo $id;?>][options][]' value="<?php echo htmlspecialchars($option);?>"  />
            <?php
            }
            ?>
          </label>
          <label>
            <div>Answers</div>
            <input type='text' name='multiple[<?php echo $id;?>][answer][0]' value="<?php echo array_search($questionArr['answer'][0], $hashed);?>" placeholder='1st right answer' />
            <input type='text' name='multiple[<?php echo $id;?>][answer][1]' value="<?php echo array_search($questionArr['answer'][1], $hashed);?>" placeholder='2nd right answer' />
          </label>
        </div>
      <?php
      }
      ?>
      <a id='newMultipleQuestion' class='button blue'>Add New Multiple Answer Question</a>
      <h2>Short Note Questions</h2>
      <?php
      foreach($questions['note'] as $id => $questionArr){
        $hashed = array();
      ?>
        <h4>Question # <?php echo $id+1;?></h4>
        <div clear style="margin-left: 20px;">
          <label>
            <div>Topic</div>
            <textarea type='text' name='note[<?php echo $id;?>][question]' id='question_input'><?php echo $questionArr['question'];?></textarea>
          </label>
          <label>
            <div>1st Set Options</div>
            <?php
            foreach($questionArr['options'][0] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='note[<?php echo $id;?>][options][0][]' value="<?php echo htmlspecialchars($option);?>"  />
            <?php
            }
            ?>
          </label>
          <label>
            <div>2nd Set Options</div>
            <?php
            foreach($questionArr['options'][1] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='note[<?php echo $id;?>][options][1][]' value="<?php echo htmlspecialchars($option);?>"  />
            <?php
            }
            ?>
          </label>
          <label>
            <div>3rd Set Options</div>
            <?php
            foreach($questionArr['options'][2] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='note[<?php echo $id;?>][options][2][]' value="<?php echo htmlspecialchars($option);?>"  />
            <?php
            }
            ?>
          </label>
          <label>
            <div>4th Set Options</div>
            <?php
            foreach($questionArr['options'][3] as $option){
              $hashed[$option] = strtoupper(hash("md5", $option));
            ?>
              <input type='text' name='note[<?php echo $id;?>][options][3][]' value="<?php echo htmlspecialchars($option);?>"  />
            <?php
            }
            ?>
          </label>
          <label>
            <div>Answer</div>
            <input type='text' name='note[<?php echo $id;?>][answer][0]' value="<?php echo array_search($questionArr['answer'][0], $hashed);?>" placeholder='1st right answer' />
            <input type='text' name='note[<?php echo $id;?>][answer][1]' value="<?php echo array_search($questionArr['answer'][1], $hashed);?>" placeholder='2nd right answer' />
            <input type='text' name='note[<?php echo $id;?>][answer][2]' value="<?php echo array_search($questionArr['answer'][1], $hashed);?>" placeholder='3rd right answer' />
            <input type='text' name='note[<?php echo $id;?>][answer][3]' value="<?php echo array_search($questionArr['answer'][2], $hashed);?>" placeholder='4th right answer' />
          </label>
        </div>
      <?php
      }
      ?>
      <a id='newShortNoteQuestion' class='button blue'>Add New Short Note Question</a>
      <h2>Practical Questions</h2>
      <?php
      foreach($questions['practical'] as $id => $questionArr){
      ?>
        <h4>Question # <?php echo $id+1;?></h4>
        <div clear style="margin-left: 20px;">
          <label>
            <div>Question</div>
            <textarea type='text' name='practical[<?php echo $id;?>][question]' id='question_input'><?php echo $questionArr['question'];?></textarea>
          </label>
        </div>
      <?php
      }
      ?>
      <a id='newPracticalQuestion' class='button blue'>Add New Practical Question</a>
      <cl></cl>
      <?php echo \H::csrf("i");?>
      <button class="red" style="padding: 13px;font-size: 20px;">SAVE QUESTIONS</button>
    </form>
    <style>
      label{
        display: block;
        margin-bottom: 10px;
      }
      #question_input{
        width: 100%;
      }
      textarea#question_input{
        min-height: 200px;
      }
    </style>
    <script>
      tmp.lastShortQuestion = <?php echo count($questions['short']) + 1;?>;
      lobby.load(function(){
        /**
         * Add Short Answer Question
         */
        $("#newShortQuestion").live("click", function(){
          id = tmp.lastShortQuestion - 1;
          var html = "<h4>Question # "+ tmp.lastShortQuestion +"</h4><div clear style='margin-left: 20px;'><label><div>Question</div><input type='text' name='short["+ id +"][question]' /></label><label><div>Options</div><input type='text' name='short["+ id +"][options][]' /><input type='text' name='short["+ id +"][options][]' /><input type='text' name='short["+ id +"][options][]' /><input type='text' name='short["+ id +"][options][]' /></label><label><div>Answer</div><input type='text' name='short["+ id +"][answer]' /><div>Paste answer above ^ (<b>case sensitive</b>).</div></label></div>";
          $(this).before(html);
          tmp.lastShortQuestion++;
        });
        
        /**
         * Add Multiple Choice Question
         */
        tmp.lastMulipleQuestion = <?php echo count($questions['multiple']) + 1;?>;
        $("#newMultipleQuestion").live("click", function(){
          id = tmp.lastMulipleQuestion - 1;
          var html = "<h4>Question # "+ tmp.lastMulipleQuestion +"</h4><div clear style='margin-left: 20px;'><label><div>Question</div><input type='text' name='multiple["+ id +"][question]' /></label><label><div>Options</div><input type='text' name='multiple["+ id +"][options][]' /><input type='text' name='multiple["+ id +"][options][]' /><input type='text' name='multiple["+ id +"][options][]' /><input type='text' name='multiple["+ id +"][options][]' /></label><label><div>Answers</div><input type='text' name='multiple["+ id +"][answer][0]' placeholder='1st right answer' /><input type='text' name='multiple["+ id +"][answer][1]' placeholder='2nd right answer' /><div>Paste answers above ^ (<b>case sensitive</b>). It will be encrypted when the form is submitted</div></label></div>";
          $(this).before(html);
          tmp.lastMulipleQuestion++;
        });
        
        /**
         * Add Short Note Question
         */
        tmp.lastShortNoteQuestion = <?php echo count($questions['note']) + 1;?>;
        $("#newShortNoteQuestion").live("click", function(){
          id = tmp.lastShortNoteQuestion - 1;
          var html = "<h4>Question # "+ tmp.lastShortNoteQuestion +"</h4><div clear style='margin-left: 20px;'><label><div>Topic</div><textarea type='text' name='note["+ id +"][question]' id='question_input'></textarea></label><label><div>1st Set Options</div><input type='text' name='note["+ id +"][options][0][]' /><input type='text' name='note["+ id +"][options][0][]' /><input type='text' name='note["+ id +"][options][0][]' /><input type='text' name='note["+ id +"][options][0][]' /></label><label><div>2nd Set Options</div><input type='text' name='note["+ id +"][options][1][]' /><input type='text' name='note["+ id +"][options][1][]' /><input type='text' name='note["+ id +"][options][1][]' /><input type='text' name='note["+ id +"][options][1][]' /></label><label><div>3rd Set Options</div><input type='text' name='note["+ id +"][options][2][]' /><input type='text' name='note["+ id +"][options][2][]' /><input type='text' name='note["+ id +"][options][2][]' /><input type='text' name='note["+ id +"][options][2][]' /></label><label><div>4th Set Options</div><input type='text' name='note["+ id +"][options][3][]' /><input type='text' name='note["+ id +"][options][3][]' /><input type='text' name='note["+ id +"][options][3][]' /><input type='text' name='note["+ id +"][options][3][]' /></label><label><div>Answer</div><input type='text' name='note["+ id +"][answer][0]' placeholder='1st right answer' /><input type='text' name='note["+ id +"][answer][1]' placeholder='2nd right answer' /><input type='text' name='note["+ id +"][answer][2]' placeholder='3rd right answer' /><input type='text' name='note["+ id +"][answer][3]' placeholder='4th right answer' /></label></div>";
          $(this).before(html);
          tmp.lastShortNoteQuestion++;
        });
        
        /**
         * Add Practical Question
         */
        tmp.lastPracticalQuestion = <?php echo count($questions['practical']) + 1;?>;
        $("#newPracticalQuestion").live("click", function(){
          id = tmp.lastPracticalQuestion - 1;
          var html = "<h4>Question # "+ tmp.lastPracticalQuestion +"</h4><div clear style='margin-left: 20px;'><label><div>Question</div><textarea type='text' name='practical["+ id +"][question]' id='question_input'></textarea></label></div>";
          $(this).before(html);
          tmp.lastPracticalQuestion++;
        });
      });
    </script>
  <?php
  }
  ?>
</div>
