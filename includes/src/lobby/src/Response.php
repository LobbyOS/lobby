<?php
/**
 * SymResponse = Symfony Response
 */
use Symfony\Component\HttpFoundation\Response as SymResponse;
use Lobby\Apps;
use Lobby\FS;
use Lobby\UI\Themes;

/**
 * Respond
 */
class Response {

  /**
   * The Symfony Response object
   */
  private static $response = null;

  /**
   * Page's content
   */
  private static $pageContent = null;

  /**
   * The <title> tag's content
   */
  protected static $title = null;

  /**
   * Set up class
   */
  public static function __constructStatic(){
    /**
     * Default response header
     */
    self::$response = new SymResponse();
    self::$response->setCharset("UTF-8");
  }

  /**
   * Set the status code of response
   * @param int $status The status code
   */
  public static function setStatusCode($status){
    self::$response->setStatusCode($status);
  }

  /**
   * Modify header
   * @return ResponseHeaderBag Symfony object
   */
  public static function header(){
    return self::$response->headers;
  }

  /**
   * Set cache headers
   * @param array $options Cache options
   */
  public static function setCache($options){
    self::$response->setCache($options);
  }

  /**
   * Set the response body
   * @param string $content The response body to set
   */
  public static function setContent($content){
    if(self::$pageContent !== null)
      self::$pageContent = null;
    self::$response->setContent($content);
  }

  /**
   * Set the page content.
   *
   * Pass the contents that should be inserted in #workspace tag inside <body>.
   *
   * @param string $content The page's HTML
   */
  public static function setPage($content){
    self::$pageContent = $content;
  }

  /**
   * Run a (PHP) file and get the response of it.
   * @param string $location Path to file
   * @param string $vars Variables that need to be passed to file
   * @return string The response of executed script
   */
  public static function getFile($location, $vars = array()){
    extract($vars);
    ob_start();
      require FS::loc($location);
    return ob_get_clean();
  }

  /**
   * Set the content by loading a file
   * @param string $location Path to file
   */
  public static function loadContent($location){
    self::setContent(self::getFile($location));
  }

  /**
   * Set the page content by loading a file
   * @param string $location Path to file
   */
  public static function loadPage($location){
    self::setPage(self::getFile($location));
  }

  /**
   * Get the response body
   * @return string|null The response body
   */
  public static function getContent(){
    return self::$response->getContent();
  }

  /**
   * Get the page response
   * @return string|null The page's HTML
   */
  public static function getPageContent(){
    return self::$pageContent;
  }

  /**
   * Whether response body is set
   * @return bool
   */
  public static function hasContent(){
    return (self::$pageContent != null || self::$response->getContent() != null);
  }

  /**
   * Display a plain error page.
   *
   * If $title and $content is not null, status code will be 500, else 404
   *
   * The respons will be sent and script execution will be stopped if this function is called.
   *
   * @param string $title Error name
   * @param string $description Error description
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

  /**
   * Send response
   */
  public static function send(){
    if(self::$pageContent !== null){
      ob_start();
        require L_DIR . "/includes/lib/lobby/inc/view.page.php";
      $html = ob_get_clean();
      self::setContent($html);
    }

    $request = Request::getRequestObject();
    if(self::$response->isNotModified($request)){
      self::setStatusCode(304);
      self::$response->prepare($request);
      self::$response->send();
    }else{
      self::$response->prepare($request);
      self::$response->send();
    }
  }

  /**
   * Make and print the <head> tag
   * @param string $title The content of <title> tag
   */
  public static function head($title = ""){
    header('Content-type: text/html; charset=utf-8');
    if($title != ""){
      self::setTitle($title);
    }

    /* Title */
    echo "<title>" . self::$title . "</title>";

    /**
     * Mobile view
     */
    echo "<meta name='viewport' content='width=device-width, initial-scale=1' />";

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

      Assets::removeJS("jquery");
      Assets::removeJS("jqueryui");
      Assets::removeJS("main");
    }

    $jsURLParams = array(
      "THEME_URL" => Themes::getThemeURL()
    );

    if(Apps::isAppRunning()){
      $jsURLParams["APP_URL"] = urlencode(Apps::getInfo("url"));
      $jsURLParams["APP_SRC"] = urlencode(Apps::getInfo("srcURL"));
    }

    $jsURL = Assets::getServeURL("js", $jsURLParams);

    echo "<script>lobby.load_script_url = '". $jsURL ."';</script>";
  }

  /**
   * Set the content of <title> tag
   * @param string $title
   * @return string
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
   * Do a redirect
   * @param string $url URL to redirect to. Can be relative to Lobby
   * @param string $status Status code to use. 302 means moved temporarily
   */
  public static function redirect($url, $status = 302){
    $url = \Lobby::u($url);
    header("Location: $url", true, $status);
    exit;
  }

}
