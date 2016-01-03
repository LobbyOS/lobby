<?php
namespace chatkin;

class ChatBase {
  
  public $cookies = "";
  public $s_server = "http://server.lobby.sim/services/chatkin/"; // Service Server - https://lobby-subins.rhcloud.com
  
  public function post($url, $params = array()){
    $this->cookies();
    $ch = curl_init();
    
    $fields_string = "";
    if(count($params) != 0){
      foreach($params as $key => $value){
        $fields_string .= "{$key}={$value}&";
      }
      /* Remove Last & char */
      rtrim($fields_string, '&');
    }
    
    $cookie_jar = realpath(APP_DIR . "/src/Data/cookies.txt"); //tempnam(sys_get_temp_dir(), "Lobby")
    file_put_contents($cookie_jar, $this->cookies);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if(count($params) != 0){
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    }
    curl_setopt($ch, CURLOPT_CAINFO, L_DIR . "/includes/src/ca_bundle.crt");
    
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_REFERER, "http://www.facebook.com");

    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
    
    $output = curl_exec($ch);

    if(curl_errno($ch)){
      die("error");
    }
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    $newcookies = file_get_contents($cookie_jar);
    $this->cookies($newcookies);
    
    $response = array($output, $info);
    return $response;
  }
  
  public function getCookie($n){
    preg_match("/". $n ."(.*?)\s(.*?)\z/", $this->cookies, $m);
    return isset($m[1]) ? trim($m[1]) : null;
  }
}

class Facebook extends \chatkin\ChatBase {
  
  public $server = "https://m.facebook.com";
  
  public function cookies($new = false){
    if($new != false){
      $this->cookies = $new;
      saveData("network_facebook_cookies", base64_encode($new));
    }else{
      $this->cookies = base64_decode(getData("network_facebook_cookies"));
    }
  }
  
  public function login($username, $password){
    $this->post($this->server);
    $response = $this->post($this->server . "/login.php", array(
      "email" => $username,
      "pass" => $password,
      "charset_test" => "%E2%82%AC%2C%C2%B4%2C%E2%82%AC%2C%C2%B4%2C%E6%B0%B4%2C%D0%94%2C%D0%84",
      "m_ts" => $this->getCookie("m_ts"),
      "li" => $this->getCookie("li")
    ));
    
    $parts = parse_url($response[1]['url']);
    if($parts['path'] == "/login.php" && isset($parts['query']) && preg_match("/\&email\=/", $parts['query'])){
      return false;
    }else{
      saveData("network_facebook_user", $this->getCookie("c_user"));
      return true;
    }
  }
  
  public function friendsCount(){
    $r = $this->post($this->server . "/me?ref=bookmarks&user=" . getData("network_facebook_user"));
    preg_match('/\_52je\s\_52j9\\\(.*?)\\\/', $r[0], $m);
    $count = substr($m[1], 2);
    return $count;
  }
  
  public function friends($start = 50){
    for($i = 0;$i <= $start;$i += 30){
      $r = $this->post($this->server . "/profile.php?__user=" . getData("network_facebook_user") . "&v=friends&__ajax__=&startindex=$i");
      preg_match_all("/aria\-label\=\\\"(.*?)\"(.*?)id\&quot\;\:(.*?)\,/", $r[0], $m);
    }
  }
}
