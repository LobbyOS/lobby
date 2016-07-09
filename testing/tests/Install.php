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
  
  /**
  public function testCSSLoad(){
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/");
    $src = $this->driver->getPageSource();
    $cssURL = $this->driver->findElement(WebDriverBy::cssSelector("link"))->getAttribute("href");
    
    $this->driver->get($cssURL);
    $this->assertContains("#workspace", $this->driver->getPageSource());
  }*/

	public function testSteps(){
    /**
     * Install intro
     */
    $this->driver->get("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/");
    $this->driver->findElement(WebDriverBy::cssSelector("a.btn"))->click();
    
    sleep(0.5);
    /**
     * Step 1
     */
    $this->assertContains("is writable", $this->driver->getPageSource());
    $this->driver->findElement(WebDriverBy::cssSelector("a.btn"))->click();
    
    /**
     * Step 2
     */
    // Choose MySQL
    $this->driver->findElement(WebDriverBy::cssSelector("a.green"))->click();
    
    $this->driver->findElement(WebDriverBy::cssSelector("input[name=dbhost]"))->clear()->sendKeys("localhost");
    $this->driver->findElement(WebDriverBy::cssSelector("input[name=dbname]"))->sendKeys(DB_NAME);
    $this->driver->findElement(WebDriverBy::cssSelector("input[name=dbusername]"))->sendKeys(DB_USERNAME);
    $this->driver->findElement(WebDriverBy::cssSelector("input[name=dbpassword]"))->sendKeys(DB_PASSWORD);
    $this->driver->findElement(WebDriverBy::cssSelector("input[name=prefix]"))->clear()->sendKeys("lobby". rand(0,2000) ."_");
    $this->driver->findElement(WebDriverBy::cssSelector("button[name=submit]"))->click();
    
    $this->assertContains("Success", $this->driver->getPageSource());
  }
  
  public function tearDown(){
    $this->driver->close();
  }
  
  public static function tearDownAfterClass(){
    system('rm -rf ' . escapeshellarg(WEB_SERVER_DOCROOT), $retval);
  }
  
}
