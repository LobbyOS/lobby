<?php
namespace Lobby\App;

class sige extends \Lobby\App {
  
  public $themes = array(
    "architect"
  );
  
  public function page($p){
    require APP_DIR . "/src/inc/class.site.php";
    
    $pages = array(
      "index", "/new"
    );
    $p = $p == "/" ? "index" : $p;
    
    if(array_search($p, $pages) !== false){
      return "auto";
    }else if(substr($p, 0, 6) == "/site/"){
      $parts = explode("/", $p);
      $site = urldecode($parts[2]);
      $name = $site;
      $site = $this->getSite();
      
      if(count($site) == 0){
        return false;
      }else{
        $p2 = isset($parts[3]) ? $parts[3] : "site";
        
        if($p2 == "site" || $p2 == "settings" || $p2 == "pages" || $p2 == "edit"){
          $site = $this->getSite($name);
          return $this->inc("/src/page/$p2.php", array(
            "name" => $name,
            "site" => $site,
            "su" => $this->u("/site/".urlencode($name)), // Short for site URL
            "page" => $p2
          ));
        }else{
          return false;
        }
      }
    }else{
      return false;
    }
  }
  
  public function addSite($name, $tagline, $out, $theme, $empty = 0, $titleTag = 1){
    $sites = $this->getSite();
    $sites[$name] = array(
      "out" => $out,
      "theme" => $theme,
      "empty" => $empty,
      "tagline" => $tagline,
      "titleTag" => $titleTag
    );
    $sites = json_encode($sites);
    saveData("sites", $sites);
  }
  
  public function getSite($site = false){
    $sites = getData("sites");
    $sites = json_decode($sites, true);
    $sites = !is_array($sites) ? array() : $sites;
    if($site){
      $sites[$site]['name'] = $site;
      return isset($sites[$site]) ? $sites[$site] : array();
    }else{
      return $sites;
    }
  }
  
  /* Get pages of a site or a specific page of a site. The name of a page is alphanumeric */
  public function getPages($site, $type = "all"){
    $data = getData("{$site}Pages");
    $pages = $data != null ? json_decode($data, true) : array();
    
    if($pages){
      if($type == "all"){
        // Give all pages
        return $pages;
      }else{
        // Give the single page requested
        $type = strtolower($type);
        return $pages[$type];
      }
    }else{
      return $pages;
    }
  }
  
  public function addPage($site, $name, $page){
    $data = getData("{$site}Pages");
    $name = strtolower($name);
    if($data){
      $pages = json_decode($data, true);
      $pages = !is_array($pages) ? array() : $pages;
    }else{
      $pages = array();
    }
    $pages[$name] = $page;
    saveData("{$site}Pages", json_encode($pages));
    return true;
  }
}
