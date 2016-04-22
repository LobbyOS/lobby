<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

class TestPrereqCheckerCheckClass extends \Prereq\PrereqCheck {
	public function check($shouldFail = false) {
		if ($shouldFail) {
			$this->result->setFailed('failed!');
		}
	}
}

class TestPrereqCheckerDummyClass {

}

class PrereqCheckerTest extends PHPUnit_Framework_TestCase {
	private $pc;
	protected function setUp() {
		$this->pc = new \Prereq\PrereqChecker();
	}

	public function testConstruct() {
		$this->assertInstanceOf('Prereq\PrereqChecker',$this->pc);
	}

	public function testSetMode() {
		$this->pc->setMode('web');
		$this->assertEquals('web',$this->pc->getMode());
	}

	public function testGetCheck() {
		$check = $this->pc->getCheck('php_version');
		$this->assertInstanceOf('Prereq\PhpVersionPrereqCheck',$check);
	}

	/**
	 * @expectedException  Exception
	 * @depends testGetCheck
	 */
	public function testGetCheckException() {
		$check = $this->pc->getCheck('unknown_check_xxx');
	}

	/**
	 * @depends testGetCheck
	 */
	public function testRegisterCheck() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerCheckClass');
		$this->assertInstanceOf('TestPrereqCheckerCheckClass',$this->pc->getCheck('my_check'));
	}

	/**
	 * @expectedException  Exception
	 * @depends testGetCheck
	 */
	public function testRegisterCheckUnknownClass() {
		$this->pc->registerCheck('my_check','ABCDRS3');
	}

	/**
	 * @expectedException  Exception
	 * @depends testGetCheck
	 */
	public function testRegisterCheckWrongClass() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerDummyClass');
	}


	/**
	 * @depends testGetCheck
	 * @depends testRegisterCheck
	 */
	public function testCheckMandatorySuccess() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerCheckClass');
		$res = $this->pc->checkMandatory('my_check',false);
		$this->assertInstanceOf('Prereq\CheckResult',$res);
		$this->assertTrue($res->success());
	}


	/**
	 * @depends testGetCheck
	 * @depends testRegisterCheck
	 */
	public function testCheckOptionalSuccess() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerCheckClass');
		$res = $this->pc->checkOptional('my_check',false);
		$this->assertInstanceOf('Prereq\CheckResult',$res);
		$this->assertTrue($res->success());
	}

	/**
	 * @depends testCheckMandatorySuccess
	 */
	public function testCheckMandatoryFailed() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerCheckClass');
		$res = $this->pc->checkMandatory('my_check',true);
		$this->assertInstanceOf('Prereq\CheckResult',$res);
		$this->assertTrue($res->failed());
	}

	/**
	 * @depends testCheckOptionalSuccess
	 */
	public function testCheckOptionalFailed() {
		$this->pc->registerCheck('my_check','TestPrereqCheckerCheckClass');
		$res = $this->pc->checkOptional('my_check',true);
		$this->assertInstanceOf('Prereq\CheckResult',$res);
		$this->assertTrue($res->failed());
	}
}
