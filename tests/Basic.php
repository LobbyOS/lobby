<?php
class Basic extends PHPUnit_Extensions_Selenium2TestCase {
  
  public function setUp(){
    /**
     * Configure Selenium server
     */
    $this->setHost("localhost");
    $this->setPort(4444);
    $this->setBrowserUrl("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT);
    $this->setBrowser('firefox');
  }

	public function testCSSLoads() {
    var_dump($this->source());
  }
  
  public function tearDown(){
    $this->stop();
  }
  
}
