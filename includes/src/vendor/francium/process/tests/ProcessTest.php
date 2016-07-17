<?php
use Fr\Process;

class ProcessTest extends PHPUnit_Framework_TestCase {

  public function testStartAProcess(){
    $tmpFile = tempnam(sys_get_temp_dir(), "FranciumProcess");
    $PR = new Process(Process::getPHPExecutable(), array(
      "arguments" => array(
        "-r" => "echo 'hello';echo 'world';"
      ),
      "output" => $tmpFile
    ));
    $PR->start();
    
    $this->assertNotEquals("helloworld", file_get_contents($tmpFile));
    
    /**
     * Let the bg process complete
     */
    sleep(1);
    $this->assertEquals("helloworld", file_get_contents($tmpFile));
    
    // Remove temporary file
    unlink($tmpFile);
  }
  
  public function testStopAProcess(){
    $tmpFile = tempnam(sys_get_temp_dir(), "FranciumProcess");
    $PR = new Process(Process::getPHPExecutable(), array(
      "arguments" => array(
        "-r" => "file_put_contents('$tmpFile', 'hello');sleep('3');file_put_contents('$tmpFile', 'world');"
      )
    ));
    $PR->start();
    
    /**
     * Let the bg process start
     */
    sleep(1);
    $this->assertEquals("hello", file_get_contents($tmpFile));
    
    var_dump($PR->stop());
    
    /**
     * It would take 5 seconds for bg process to complete
     */
    sleep(3);
    $this->assertNotEquals("world", file_get_contents($tmpFile));
    
    // Remove temporary file
    unlink($tmpFile);
  }

}
