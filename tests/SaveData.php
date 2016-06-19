<?php
class SaveData extends PHPUnit_Extensions_Selenium2TestCase {
  
  public function setUp(){
    /**
     * Setup DB
     */
    
  }

	public function testCSSLoads() {
    return true;
  }
  
  public function tearDown(){
    $this->stop();
  }
  
}
