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
      require_once L_DIR . "/includes/lib/lobby/inc/page.php";
    $html = ob_get_clean();
    self::setContent($html);
  }
  
  public static function loadPage($location){
    ob_start();
      require_once FS::loc($location);
    $html = ob_get_clean();
    self::setPage($html);
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
  
  public static function send(){
    self::$response->prepare(Request::getRequestObject());
    self::$response->send();
  }

}
