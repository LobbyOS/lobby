<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

class PhpVersionPrereqCheckTest extends PHPUnit_Framework_TestCase {
	public function testCheckRegisteredAsInternal() {
		$pc = new \Prereq\PrereqChecker();
		$check = $pc->getCheck('php_version');
		$this->assertInstanceOf('Prereq\PhpVersionPrereqCheck',$check);
	}

	public function testCheck() {
		$dc = new \Prereq\PhpVersionPrereqCheck();
		$dc->check('>','5.2.0');
		$this->assertTrue($dc->getResult()->success());

		$dc->check('<','5.2.0');
		$this->assertTrue($dc->getResult()->failed());
		$this->assertEquals("Actual PHP Version (".phpversion().") does not meet the requirement < 5.2.0",$dc->getResult()->message);
	}
}
