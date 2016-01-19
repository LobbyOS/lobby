<?php
if( isset($_POST['length']) ){
  $lHTML = "";
  $avail = array();
  $pWords = array();
  
  $chars = 'abcdeabcdefghijklmnopqrstuvwxyzlmnopqrstuvwxyzfghijklmnopabcdefghijklmnopqrstuabcdefghijklmnopqrstuvwxyzlmnopqrstuvwxyzvwxyzqrstuvwxyzabcdefghijkabcdefghijklmnopqrstuvwxyzlmnopabcdefghijklmnopqrstuvwxyzlmnopqrstuvwxyzqrstuvwxyz';
  $size = strlen($chars);
  
  for($i = 0;$i < $_POST['length'];$i++){
    $str = $chars[rand(0, $size-1)];
    $lHTML .= "<a href='' class='letter'>$str</a>";
    $avail[] = $str;
  }
  
  /**
   * Get Dictionary
   */
  $dict = file_get_contents(APP_DIR . "/src/data/wordlist.txt");
  $dict = explode("\n", $dict);
  
  /**
   * Loop through letters of each word in dictionary
   */
  foreach($dict as $word){
    $checked = array();
    $valid = true;
    $letters = str_split($word);
    
    $wordAvail = $avail;
    foreach($letters as $letter){
      $key = array_search($letter, $wordAvail);
      if($key === false){
        $valid = false;
      }
      unset($wordAvail[$key]);
    }
    if($valid == true){
      $pWords[$word] = 1;
    }
  }
  
  $kpWords = array_keys($pWords);
  sort($kpWords);
  array_multisort(array_map('strlen', $kpWords), SORT_ASC, $pWords);
  
  
  /* Wrap it up */
  $arr = array(
    "str" => $lHTML,
    "words" => $pWords
  );
  echo json_encode($arr);
}
?>
