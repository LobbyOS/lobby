<?php
/**
 * A Functions file that are simple aliases of class methods
 * Example :
 * \Lobby::getOption() can be used with just getOption()
 *
 * Also contains Non Class functions
 */

/**
 * Show Error Messages
 */
function ser($title = null, $description = ""){
  $html = "";
  if($title === null){
    Response::showError();
  }else{
    $html .= "<div class='message'>";
      $html .= "<div style='color:red;' class='title'>$title</div>";
      if($description != ""){
        $html .= "<div style='color:red;'>$description</div>";
      }
    $html .= "</div>";
  }
  return $html;
}

/**
 * Show Success Messages
 */
function sss($title, $description){
  $html = "<div class='message'>";
  if($title == ""){
    $html .= "<div style='color:green;' class='title'>Success</div>";
  }else{
    $html .= "<div style='color:green;' class='title'>$title</div>";
  }
  if($description != ""){
    $html .= "<div style='color:green;'>$description</div>";
  }
  $html .= "</div>";
  return $html;
}

/* Show Messages */
function sme($title, $description){
  $html = "<div class='message'>";
  if($title == ""){
    $html .= "<div style='color:black;' class='title'>Message</div>";
  }else{
    $html .= "<div style='color:black;' class='title'>$title</div>";
  }
  if($description != ""){
    $html .= "<div style='color:black;'>$description</div>";
  }
  $html .= "</div>";
  return $html;
}

/* A map of $db->filt() that strips out HTML content */
function filt($string){
  return \Lobby\DB::filt( urldecode($string) );
}

function __($text, $domain = 'main'){
	return \Lobby\l10n::__($text, $domain);
}

function _e($text, $domain = 'main'){
	echo \Lobby\l10n::__($text, $domain);
}
