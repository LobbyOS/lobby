<?php
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class LobbyTest extends PHPUnit_Framework_TestCase {
  
  public function setUp(){
    /**
     * Start server
     */
    //$web = RemoteWebDriver::create("http://". WEB_SERVER_HOST . ":" . WEB_SERVER_PORT, DesiredCapabilities::firefox());
    //$web->quit();
  }

	public function test() {
		$this->assertEquals(1+1, 2);
    return true;
  }
  
}
