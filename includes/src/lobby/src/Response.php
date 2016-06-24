<?php
/**
 * SymResponse = Symphony Response
 */
use Symfony\Component\HttpFoundation\Response as SymResponse;
use Lobby\Apps;
use Lobby\FS;
use Lobby\UI\Themes;

class Response {

  private static $response = null;
  private static $pageContent = null;
  protected static $title = null;

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
    if(self::$pageContent !== null)
      self::$pageContent = null;
    self::$response->setContent($content);
  }
  
  public static function setPage($content){
    self::$pageContent = $content;
  }
  
  public static function getFile($location, $vars = array()){
    extract($vars);
    ob_start();
      require FS::loc($location);
    return ob_get_clean();
  }
  
  public static function loadPage($location){
    self::setPage(self::getFile($location));
  }
  
  public static function getContent(){
    return self::$response->getContent();
  }
  
  public static function getPageContent(){
    return self::$pageContent;
  }
  
  public static function hasContent(){
    return (self::$pageContent != null || self::$response->getContent() != null);
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
    if(self::$pageContent !== null){
      ob_start();
        require L_DIR . "/includes/lib/lobby/inc/view.page.php";
      $html = ob_get_clean();
      self::setContent($html);
    }
    
    self::$response->prepare(Request::getRequestObject());
    self::$response->send();
  }
  
  /**
   * Print the <head> tag
   */
  public static function head($title = ""){
    header('Content-type: text/html; charset=utf-8');
    if($title != ""){
      self::setTitle($title);
    }
    
    if(Assets::issetJS('jquery')){
      /**
       * Load jQuery, jQuery UI, Lobby Main, App separately without async
       */
      $url = L_URL . "/includes/serve-assets.php?type=js&assets=" . implode(",", array(
        Assets::getJS('jquery'),
        Assets::getJS('jqueryui'),
        Assets::getJS('main'),
        Assets::issetJS('app') ? Assets::getJS('app') : ""
      ));
      echo "<script src='{$url}'></script>";
      
      Assets::removeJs("jquery");
      Assets::removeJs("jqueryui");
      Assets::removeJs("main");
    }
    
    $jsURLParams = array(
      "THEME_URL" => Themes::getURL()
    );
    
    if(Apps::isAppRunning()){
      $jsURLParams["APP_URL"] = urlencode(Apps::getInfo("url"));
      $jsURLParams["APP_SRC"] = urlencode(Apps::getInfo("srcURL"));
    }
    
    $jsURL = Assets::getServeURL("js", $jsURLParams);
    
    echo "<script>lobby.load_script_url = '". $jsURL ."';</script>";
    
    $cssServeParams = array(
      "THEME_URL" => THEME_URL
    );
    
    /**
     * CSS Files
     */
    if(Apps::isAppRunning()){
      $cssServeParams["APP_URL"] = urlencode(Apps::getInfo("url"));
      $cssServeParams["APP_SRC"] = urlencode(Apps::getInfo("srcURL"));
    }
    echo Assets::getServeLinkTag($cssServeParams);
    
    echo "<link href='". L_URL ."/favicon.ico' sizes='16x16 32x32 64x64' rel='shortcut icon' />";
    
    /* Title */
    echo "<title>" . self::$title . "</title>";
  }
 
  /**
   * Set the Page title
   */
  public static function setTitle($title = ""){
    if($title != ""){
      self::$title = $title;
      if(self::$title == ""){
        self::$title = "Lobby";
      }else{
        self::$title .= " - Lobby";
      }
    }
  }
  
  /**
   * A redirect function that support HTTP status code for redirection 
   * 302 = Moved Temporarily
   */
  public static function redirect($url, $status = 302){
    $url = \Lobby::u($url);
    header("Location: $url", true, $status);
    exit;
  }

}
