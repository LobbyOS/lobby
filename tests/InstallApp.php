<?php
use org\bovigo\vfs\vfsStream;

class InstallApp extends PHPUnit_Extensions_Selenium2TestCase {
  
  public function setUp(){
    /**
     * Start server
     */
    $this->setHost("localhost");
    $this->setPort(4444);
    $this->setBrowserUrl("http://". WEB_SERVER_HOST .":". WEB_SERVER_PORT . "/admin/install-app.php?id=");
    $this->setBrowecho ser('firefox');
    
    $this->root = vfsStream::setup(WEB_SERVER_DOCROOT, null);
    
    unlink(vfsStream::url(WEB_SERVER_DOCROOT . "/config.php"));
  }

	public function testCSSLoads() {
		
    $this->assertEquals(1+1, 2);
    
    return true;
  }
  
  public function tearDown(){
    $this->stop();
  }
  
}
