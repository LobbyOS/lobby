<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');
class DbPdoCheckMock extends \Prereq\DbPdoConnectionPrereqCheck {
	public $dsn;
	public $user;
	public $pw;
	public $shouldFail = false;

	protected function pdoConnect($dsn,$user,$pw) {
		$this->dsn = $dsn;
		$this->user = $user;
		$this->pw = $pw;
		if ($this->shouldFail) {
			throw new PDOException('Failed');
		} else return true;
	}
}

class DbPdoConnectionPrereqCheckTest extends PHPUnit_Framework_TestCase {
	private $pc;

	protected function setUp() {
		$this->pc = new \Prereq\PrereqChecker();
	}

	public function testCheckRegisteredAsInternal() {
		
		$check = $this->pc->getCheck('db_pdo_connection');
		$this->assertInstanceOf('Prereq\DbPdoConnectionPrereqCheck',$check,'db_pdo_connection not registered as internal Check.');
	}

	/**
	 * @depends testCheckRegisteredAsInternal
	 */
	public function testParamCall() {
		$check = new DbPdoCheckMock('DbMock');
		$check->shouldFail = false;
		$check->check(array(
			'dsn' => 'pgsql:dbname=test host=127.0.0.1',
			'username' => 'test',
			'password' => 'testpw'
		));

		$this->assertEquals('pgsql:dbname=test host=127.0.0.1', $check->dsn);
		$this->assertEquals('test', $check->user);
		$this->assertEquals('testpw',$check->pw);
	}

	/**
	 * @depends testParamCall
	 */
	public function testCheckSuccess() {
		$check = new DbPdoCheckMock('DbMock');
		$check->shouldFail = false;
		$check->check(array(
			'dsn' => 'pgsql:dbname=test host=127.0.0.1',
			'username' => 'test',
			'password' => 'testpw'
		));

		$this->assertTrue($check->getResult()->success());
	}

	/**
	 * @depends testParamCall
	 */
	public function testCheckFailed() {
		$check = new DbPdoCheckMock('DbMock');
		$check->shouldFail = true;
		$check->check(array(
			'dsn' => 'pgsql:dbname=test host=127.0.0.1',
			'username' => 'test',
			'password' => 'testpw'
		));

		$this->assertTrue($check->getResult()->failed());
	}
}
