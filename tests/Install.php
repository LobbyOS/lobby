<?php

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class Install extends PHPUnit_Framework_TestCase {
  
  public function setUp(){
    /**
     * Configure Selenium server
     */
    $this->driver = RemoteWebDriver::create("http://localhost:4444/wd/hub", DesiredCapabilities::firefox());
  }
  
  public function testCSSLoad(){
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/");
    $src = $this->driver->getPageSource();
    $cssURL = $this->driver->findElement(WebDriverBy::cssSelector("link"))->getAttribute("href");
    
    $this->driver->get($cssURL);
    $this->assertContains("#workspace", $this->driver->getPageSource());
  }

	public function testStepOne(){
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/");
    $this->driver->findElement(WebDriverBy::cssSelector("a.btn"))->click();
  }
  
  public function testStepTwo(){
    $this->driver->findElement(WebDriverBy::cssSelector("a.btn"))->click();
  }
  
  public function tearDown(){
    $this->driver->close();
  }
  
  public static function tearDownAfterClass(){
    system('rm -rf ' . escapeshellarg(WEB_SERVER_DOCROOT), $retval);
  }
  
}
