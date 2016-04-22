<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

class MockPhpExtCheck extends \Prereq\PhpExtensionPrereqCheck {
	public $exists = true;
	protected function extension_loaded($ext) {
		return $this->exists;
	}
}

class PhpExtensionPrereqCheckTest extends PHPUnit_Framework_TestCase {
	public function testCheckRegisteredAsInternal() {
		$pc = new \Prereq\PrereqChecker();
		$check = $pc->getCheck('php_extension');
		$this->assertInstanceOf('Prereq\PhpExtensionPrereqCheck',$check);
	}

	public function testCheckAvailable() {
		$dc = new MockPhpExtCheck();
		$dc->exists = true;
		$dc->check('MyMockExtension');
		$this->assertTrue($dc->getResult()->success());
	}

	public function testCheckNotAvailable() {
		$dc = new MockPhpExtCheck();
		$dc->exists = false;
		$dc->check('MyMockExtension');
		$this->assertTrue($dc->getResult()->failed());
		$this->assertEquals("Extension 'MyMockExtension' not loaded.",$dc->getResult()->message);
	}
}
