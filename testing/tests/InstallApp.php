<?php
use Facebook\WebDriver\WebDriverBy;
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
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/admin/lobby-store.php?app=anagram");
    $this->driver->findElement(WebDriverBy::cssSelector("#leftpane .btn.red"))->click();
    
    new WebDriverWait($this->driver, 5);
    
    $this->assertContains($this->driver->getPageSource(), "installed");
  }
  
  public function tearDown(){
    $this->driver->close();
  }
  
}
