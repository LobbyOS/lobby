<?php
/**
 * SymResponse = Symphony Response
 */
use Symfony\Component\HttpFoundation\Response as SymResponse;
use \Lobby\FS;

class Response {

  private static $response = null;
  private static $pageContent = null;

  public static function __constructStatic(){
    /**
     * Default response header
     */
    self::$response = new SymResponse();
    self::$response->setCharset("UTF-8");
  }
  
  public static function setStatusCode($status){
    self::$response->setStatusCode($status);
  }
  
  public static function setContent($content){
    self::$response->setContent($content);
  }
  
  public static function setPage($content){
    self::$pageContent = $content;
    
    ob_start();
      require_once L_DIR . "/includes/lib/lobby/inc/view.page.php";
    $html = ob_get_clean();
    self::setContent($html);
  }
  
  public static function getFile($location, $vars = array()){
    extract($vars);
    ob_start();
      require_once FS::loc($location);
    return ob_get_clean();
  }
  
  public static function loadPage($location){
    self::setPage(self::getFile($location));
  }
  
  public static function getContent(){
    return self::$content;
  }
  
  public static function getPageContent(){
    return self::$pageContent;
  }
  
  public static function hasContent(){
    return self::$response->getContent() !== null;
  }
  
  /**
   * Display a plain error page
   * Default: 400, If $title & $content is passed, 500
   */
  public static function showError($title = null, $description = null){
    if($title === null){
      self::setStatusCode(400);
      $title = "404 Not Found";
      $description = "The requested path was not found in Lobby";
    }else{
      self::setStatusCode(500);
    }
    
    self::setContent(self::getFile("includes/lib/lobby/inc/view.error.php", array(
      "title" => $title,
      "description" => $description
    )));
    
    self::send();
    exit;
  }
  
  public static function send(){
    self::$response->prepare(Request::getRequestObject());
    self::$response->send();
  }

}
