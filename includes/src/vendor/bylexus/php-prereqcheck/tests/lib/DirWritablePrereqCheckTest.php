<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

class DirWritablePrereqCheckTest extends PHPUnit_Framework_TestCase {
	public function testCheckRegisteredAsInternal() {
		$pc = new \Prereq\PrereqChecker();
		$check = $pc->getCheck('dir_writable');
		$this->assertInstanceOf('Prereq\DirWritablePrereqCheck',$check);
	}

	public function testCheckWritable() {
		$tmpfile = tempnam('/tmp/test', 'foo');
		$dc = new \Prereq\DirWritablePrereqCheck();
		$dc->check(dirname($tmpfile));
		@unlink($tmpfile);
		$this->assertTrue($dc->getResult()->success());
	}

	public function testCheckNonWritable() {
		$dc = new \Prereq\DirWritablePrereqCheck();
		$dirname = '/non-existing-dir/'.time();
		$dc->check($dirname);
		$this->assertTrue($dc->getResult()->failed());
		$this->assertEquals("Directory '{$dirname}' not writable.",$dc->getResult()->message);
	}
}
