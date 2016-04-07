<?php
/**
 * PHP Prerequisite Checker
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */

namespace Prereq;

/**
 * A Prerequisites checker for PHP. It enables the user to easily check for
 * application pre-requisites, whatever that may be. This checker comes with some pre-defined
 * checkers:
 *
 * php_version: checks if the actual php version is egilible
 * php_extension: checks if a given php extension is loaded
 * php_ini: helper for checking php ini variables
 * dir_writable: checks if the specified dir is writable
 *
 * A brief example:
 *
 * $pc = new PrereqChecker();
 * $pc->check('php_version','>=','5.3.0');
 *
 * Outputs the results either on command line or as web output, or keeps silent.
 */
class PrereqChecker {
    const RES_PASSED = 'passed';
    const RES_WARNING = 'warning';
    const RES_FAILED  = 'failed';

    private $_mode;
    private $_checks = array();
    private $_checkResults = array();

    public function __construct() {
        if (strtolower(php_sapi_name()) === 'cli') {
            $this->_mode = 'cli';
        } else {
            $this->_mode = 'web';
        }

        $this->addInternalChecks();
        $this->reset();
    }

    public static function autoload($name) {
        $path = explode('\\',$name);
        if (count($path) == 2 && $path[0] == 'Prereq') {
            require_once(__DIR__.DIRECTORY_SEPARATOR.$path[1].'.php');
        }
    }

    private function addInternalChecks() {
        $this->registerCheck('php_version', '\Prereq\PhpVersionPrereqCheck');
        $this->registerCheck('php_extension', '\Prereq\PhpExtensionPrereqCheck');
        $this->registerCheck('php_ini', '\Prereq\PhpIniPrereqCheck');
        $this->registerCheck('dir_writable', '\Prereq\DirWritablePrereqCheck');
        $this->registerCheck('db_pdo_connection', '\Prereq\DbPdoConnectionPrereqCheck');
    }

    public function setMode($mode) {
        if (in_array($mode,array('cli','web','silent'))) {
            $this->_mode = $mode;
        } else throw new \Exception("Mode must be one of 'web','cli'");
    }

    public function getMode() {
        return $this->_mode;
    }


    public function registerCheck($checkName, $checkClass) {
        if (!class_exists($checkClass)) throw new \Exception("Class not found: ".$checkClass);
        $parents = class_parents($checkClass);
        if (!in_array('Prereq\PrereqCheck', $parents)) throw new \Exception('Class does not inherit PrereqCheck');
        $this->_checks[$checkName] = $checkClass;
    }

    public function getCheck($checkName) {
        if (array_key_exists($checkName, $this->_checks)) {
            $className = $this->_checks[$checkName];
            if (class_exists($className)) {
                $checker = new $className();
                if ($checker instanceof PrereqCheck) {
                    return $checker;
                }
            }
        }
        throw new \Exception('Check class for '.$checkName.' not found.');
    }

    public function checkMandatory($checkName) {
        $arg_list = func_get_args();
        array_shift($arg_list);
        return $this->check($checkName, self::RES_FAILED, $arg_list);
    }

    public function checkOptional($checkName) {
        $arg_list = func_get_args();
        array_shift($arg_list);
        return $this->check($checkName, self::RES_WARNING,$arg_list);
    }

    protected function check($checkName, $severity = self::RES_FAILED, $arguments) {
        $checker = $this->getCheck($checkName);
        call_user_func_array(array($checker,'check'), $arguments);
        $ret = $checker->getResult();
        $this->outputCheckResult($ret, $severity);
        $this->_checkResults[$ret->success()?self::RES_PASSED:$severity] = $ret;
        return $ret;
    }

    public function reset() {
        $this->_checkResults = array(
            self::RES_PASSED => array(),
            self::RES_WARNING => array(),
            self::RES_FAILED => array()
        );
    }

    public function didAllSucceed() {
        if (count($this->_checkResults[self::RES_FAILED]) > 0) {
            return false;
        }
        return true;
    }

    protected function outputCheckResult(CheckResult $res, $severity) {
        if ($this->_mode === 'cli') {
            $this->writeOutputCli($res, $severity);
        } else if ($this->_mode === 'web') {
            $this->writeOutputWeb($res, $severity);
        }
    }

    protected function writeOutputCli(CheckResult $res, $severity) {
        $str = "\033[0m{$res->check->name}: ";
        if ($res->success()) {
            $str .= "\033[0;32mPASSED\033[0m";
        } else {
            if ($severity === self::RES_FAILED) {
                $str .= "\033[0;31mFAILED: \033[0m{$res->message}";
            } else {
                $str .= "\033[0;33mWARNING: \033[0m{$res->message}";    
            }
        }
        echo "{$str}\n";
    }

    protected function writeOutputWeb(CheckResult $res, $severity) {
        $str = "<div>{$res->check->name}: ";
        if ($res->success()) {
            $str .= "<span style=\"color: #00FF00\">PASSED</span>";
        } else  {
            if ($severity === self::RES_FAILED) {
                $str .= "<span style=\"color: #FF0000\">FAILURE: </span>{$res->message}";
            } else {
                $str .= "<span style=\"color: #FFFF00\">WARNING: </span>{$res->message}";
            }
        }
        echo "{$str}</div>";
    }
}
