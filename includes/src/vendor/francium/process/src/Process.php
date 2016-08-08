<?php
namespace Fr;

/**
.---------------------------------------------------------------------------.
| The Francium Project                                                      |
| ------------------------------------------------------------------------- |
| This software 'Process' is a part of the Francium (Fr) project.           |
| http://subinsb.com/the-francium-project                                   |
| ------------------------------------------------------------------------- |
|     Author: Subin Siby                                                    |
| Copyright (c) 2014 - 2015, Subin Siby. All Rights Reserved.               |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Apache License, Version 2.0              |
|            http://www.apache.org/licenses/LICENSE-2.0                     |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/

/**
.---------------------------------------------------------------------------.
|  Software:      Francium Process                                          |
|  Version:       0.3.1 (Last Updated on 2016 May 07)                       |
|  Contact:       http://github.com/subins2000/Francium-Process             |
|  Documentation: https://subinsb.com/francium-process                      |
|  Support:       https://subinsb.com/francium-process                      |
'---------------------------------------------------------------------------'
*/

class Process{

  public static $os = null;
  public $cmd = null, $options = array(
    "output" => null,
    "arguments" => array()
  );
  
  private $uniqueID = null;
  
  private static function setOS(){
    if(self::$os === null){
      /**
       * Get the OS
       */
      $os = strtolower(substr(php_uname('s'), 0, 3));
      if ($os == 'lin') {
        self::$os = "linux";
      }else if ($os == 'win') {
        self::$os = "windows";
      }else if ($os == 'mac') {
        self::$os = "mac";
      }
    }
  }
  
  private static function randStr($length){
    $str="";
    $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen($chars);
    for($i = 0;$i < $length;$i++) {
      $str .= $chars[rand(0,$size-1)];
    }
    return $str;
  }
  
  /**
   * @param string $cmd The command to run
   * @param array $options Configuration array
   */
  public function __construct($cmd, $options){
    $this->cmd = $cmd;
    $this->options = array_replace_recursive($this->options, $options);
    self::setOS();
    $this->uniqueID = self::randStr(10);
  }
  
  /**
   * Callback is called when process is started
   */
  public function start($callback = null){
    if(self::$os === "linux" || self::$os === "mac"){
      return $this->startOnNix($callback);
    }else if(self::$os === "windows"){
      return $this->startOnWindows($callback);
    }
  }
  
  /**
   * @param $callback function - Callback made when process started. If this is present, then the connection between server and browser is closed too
   * @return string - The command executed
   */
  public function startOnWindows($callback){
    /**
     * Make Arguments
     */
    $arguments = "";
    foreach($this->options["arguments"] as $option => $value){
      if(is_numeric($option)){
        $arguments .= " " . escapeshellarg($value);
      }else{
        $arguments .= " $option " . escapeshellarg($value);
      }
    }
    
    /**
     * Where to output
     */
    if($this->options["output"] === null){
      $outputFile = "nul";
    }else{
      $outputFile = $this->options["output"];
    }
    $output = " > " . escapeshellarg($outputFile);
    
    $cmd = escapeshellarg($this->cmd) . $arguments . $output;
    
    $bgCmd = "start /B " . escapeshellcmd(self::getPHPExecutable()) . " " . escapeshellarg(self::getBGPath()) . " " . escapeshellarg(base64_encode($cmd)) . " ". escapeshellarg($this->uniqueID) ." > nul 2>&1";
    pclose(popen($bgCmd, "r"));
    
    /**
    $WshShell = new \COM("WScript.Shell");
    $oExec = $WshShell->Run($bgCmd, 0, false);
    */
    
    /**
     * If callback is valid, call callback,
     * then close connection
     */
    if(is_callable($callback)){
      ob_start();
      
      call_user_func($callback);
      header("Content-Length: ".ob_get_length());
      header("Connection: close");
      
      flush();
      ob_flush();
    }
    
    return $cmd;
  }
  
  /**
   * *nix systems :
   *    Linux - Ubuntu, Debian...
   *    Unix - Mac
   * @param $callback function - Callback made when process started. If this is present, then the connection between server and browser is closed too
   * @return string - The command executed
   */
  public function startOnNix($callback){
    /**
     * Make Arguments
     */
    $arguments = "";
    foreach($this->options["arguments"] as $option => $value){
      if(is_numeric($option)){
        $arguments .= " " . escapeshellarg($value);
      }else{
        $arguments .= " $option=" . escapeshellarg($value);
      }
    }
    
    /**
     * Where to output
     */
    if($this->options["output"] === null){
      $outputFile = "/dev/null";
    }else{
      $outputFile = $this->options["output"];
    }
    $output = " > " . escapeshellarg($outputFile);
    
    $cmd = escapeshellarg($this->cmd) . $arguments . $output;
    
    $bgCmd = escapeshellcmd(self::getPHPExecutable()) . " " . escapeshellarg(self::getBGPath()) . " " . escapeshellarg(base64_encode($cmd)) . " ". escapeshellarg($this->uniqueID) ." > /dev/null &";
    exec($bgCmd);
    
    /**
     * If callback is valid, call callback,
     * then close connection
     */
    if(is_callable($callback)){
      ob_start();
      
      call_user_func($callback);
      header("Content-Length: ".ob_get_length());
      header("Connection: close");
      
      flush();
      ob_flush();
    }
    
    return $cmd;
  }
  
  public function stop(){
    if(self::$os === "linux" || self::$os === "mac"){
      return exec("pid=$(ps -ef | awk '/[". substr($this->uniqueID, 0, 1) ."]". substr($this->uniqueID, 1) ."/{print $2}');if [ -z \"\$pid\" ]; then echo 1; else kill $(pstree -pn \$pid | grep -o '([[:digit:]]*)' |grep -o '[[:digit:]]*'); fi");
    }else if(self::$os === "windows"){
      return exec("wmic Path win32_process Where \"CommandLine Like '%". $this->uniqueID ."%'\" Call Terminate");
    }
  }
  
  /**
   * Get the Path to PHP binary file
   * Linux - /usr/bin/php
   */
  public static function getPHPExecutable() {
    if(defined("PHP_BINARY") && PHP_BINARY != ""){
      return PHP_BINARY;
    }else{
      $paths = explode(PATH_SEPARATOR, getenv('PATH'));
      foreach ($paths as $path) {
        // we need this for XAMPP (Windows)
        if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
          return $path;
        }else {
          $php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
          if (file_exists($php_executable) && is_file($php_executable)) {
            return $php_executable;
          }
        }
      }
    }
    return FALSE; // not found
  }
  
  private function getBGPath(){
    return __DIR__ . "/ProcessBG.php";
  }
  
}
