<?php
namespace Lobby\App;
class millionaire extends \Lobby\App {
  
  public $currency = "$";
  
  public $money = array(
    0 => "0",
    1 => "100",
    2 => "200",
    3 => "300",
    4 => "500",
    5 => "1,000",
    6 => "2,000",
    7 => "4,000",
    8 => "8,000",
    9 => "16,000",
    10 => "25,000",
    11 => "50,000",
    12 => "100,000",
    13 => "250,000",
    14 => "500,000",
    15 => "1 Million"
  );
  
  public function page($p){
    return "auto";
  }
  
  public function questions(){
    $return = array();
    $questions = json_decode($this->get("/src/data/questions.json"), true);
    
    foreach($questions as $level => $collection){
      $i = rand(0, count($collection) - 1);
      unset($collection[$i]['answer']);
      
      shuffle($collection[$i]['options']);
      $return[$level] = array(
        "id" => $i,
        "content" => $collection[$i]
      );
    }
    return $return;
  }
  
  public function money(){
    if(isset($_SESSION['app-millionaire-level'])){
      $money = $this->currency;
      $money .= $_SESSION['app-millionaire-level'] >= 10 ? $this->money[10] : ($_SESSION['app-millionaire-level'] >= 5 ? $this->money[5] : $this->money[0]);
      return $money;
    }
  }
}
