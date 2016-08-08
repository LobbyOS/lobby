<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class InstallApp extends PHPUnit_Framework_TestCase {
  
  public function setUp(){
    /**
     * Configure Selenium server
     */
    $this->driver = RemoteWebDriver::create("http://localhost:4444/wd/hub", DesiredCapabilities::firefox());
    
    exec("find '". WEB_SERVER_DOCROOT ."/contents/apps' -mindepth 1 -exec rm -rf '{}' \+");
  }
  
  public function testCSSLoad(){
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/admin/lobby-store.php");
    $src = $this->driver->getPageSource();
    $cssURL = $this->driver->findElement(WebDriverBy::cssSelector("link"))->getAttribute("href");
    
    $this->driver->get($cssURL);
    $this->assertContains("#storeNav", $this->driver->getPageSource());
  }

	public function testCanBeInstalled(){
    /**
     * Install intro
     */
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/admin/lobby-store.php?app=anagram");
    $this->assertNotEquals($this->driver->findElement(WebDriverBy::cssSelector("#leftpane .btn.red"))->getAttribute("class"), "btn red disabled");
  }
  
  public function testInstallByAJAX(){
    $this->assertEquals(file_exists(WEB_SERVER_DOCROOT . "/contents/apps/anagram/manifest.json"), false);
    
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/admin/lobby-store.php?app=anagram");
    $this->driver->findElement(WebDriverBy::cssSelector("#leftpane .btn.red"))->click();
    
    $that = $this;
    $wait = new WebDriverWait($this->driver, 20);
    $wait->until(function() use($that){
      return count($that->driver->findElements(WebDriverBy::cssSelector("#appInstallationProgress li"))) > 2;
    });
    
    $that->assertContains("Downloaded 100%", $that->driver->getPageSource());
    
    $wait = new WebDriverWait($this->driver, 20);
    $wait->until(function() use($that){
      return count($that->driver->findElements(WebDriverBy::cssSelector("#appInstallationProgress [data-status-id=install_finished]"))) !== 0;
    });
    
    $this->assertContains("Installed", $this->driver->getPageSource());
    
    $this->assertEquals(file_exists(WEB_SERVER_DOCROOT . "/contents/apps/anagram/manifest.json"), true);
  }
  
  public function tearDown(){
    $this->driver->close();
  }
  
}
