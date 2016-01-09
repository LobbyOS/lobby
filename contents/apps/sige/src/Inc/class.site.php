<?php
namespace Lobby\App\sige;

class Site {
  
  var $init = false;
  private $theme;
  private $path;
  
  public function __construct($site, $AppObject){
    $this->name = $site['name'];
    $this->path = $site['out'];
    $this->theme = $site['theme'];
    $this->delete = $site['empty'];
    $this->tagline = $site['tagline'];
    $this->titleTag = $site['titleTag'];
    $this->init = true;
    
    $this->app = $AppObject;
  }
  
  public function generate($pages){
    $out = $this->path;
    $path = APP_DIR . "/src/data/themes/{$this->theme}"; // The theme path
    
    if($this->delete == 1){
      $this->log("Emptying Output Directory");
      /**
       * Empty the Output Dir just in case
       */
      $this->recursiveRemoveDirectory($out);
      $this->log("Finished Emptying Output Directory");
    }
    
    if($this->init){
      /* Start copying the theme contents to output directory */
      $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
      foreach($objects as $location => $object){
        $name = str_replace("{$path}/", "", $location); // Make into relative path
        $outLoc = "{$out}/$name";
        
        if($object->isFile() && $name != "layout.html" && $name != "thumbnail.png" && $name != "example.html"){
          $this->log("Copying $name to Ouput Directory");
          copy($location, $outLoc);
        }elseif($object->isDir() && !file_exists($outLoc)){
          /* Make sub directories on output folder */
          mkdir($outLoc);
        }
      }
      /* Start creating pages */
      $layout = "{$path}/layout.html";

      foreach($pages as $page){
        $this->page($page['slug'], array(
          "{{page-title}}" => $page['title'],
          "{{page-content}}" => $page['body']
        ));
      }
    }
  }
  
  /* Create a page from template */
  public function page($slug = "", $values = array(), $layout = ""){
    $location = "{$this->path}/{$slug}.html";
    if($layout == ""){
      $layout = $this->app->get("/src/data/themes/{$this->theme}/layout.html");
    }
    if($this->titleTag && $values["{{page-title}}"] != $this->name){
      $layout = str_replace("<title>{{page-title}}</title>", "<title>{{page-title}} - {{site-name}}</title>", $layout);
    }
    if(isset($values['{{page-title}}']) && isset($values['{{page-content}}'])){
      $toReplace = array(
        "{{page-title}}" => "",
        "{{page-content}}" => "",
        "{{site-head}}" => "",
        "{{site-name}}" => $this->name,
        "{{site-tagline}}" => $this->tagline,
        "{{site-sidebar}}" => ""
      );
      $result = array_merge($toReplace, $values);
      foreach($result as $from => $to){
        $layout = str_replace($from, $to, $layout);
      }
      
      /* Make directory if it doesn't exist */
      $dir = dirname($location);
      if( !file_exists($dir) ){
        mkdir($dir);
      }
      
      /* Make the Page file */
      file_put_contents($location, $layout);
      return true;
    }else{
      return false;
    }
  }
  
  public static function log($m){
    return \Lobby::log($m, "app.sige.log"); // Log writing will only happen in the debugging is enabled in config.php
  }
  
  /* Recursive Directory Remover */
  public function recursiveRemoveDirectory($dir) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
      if ($file->getFilename() === '.' || $file->getFilename() === '..') {
        continue;
      }
      if ($file->isDir()){
        rmdir($file->getRealPath());
      } else {
        rm($file->getRealPath());
      }
    }
    if($dir != $this->path){
      rmdir($dir);
    }
  }
}
?>
