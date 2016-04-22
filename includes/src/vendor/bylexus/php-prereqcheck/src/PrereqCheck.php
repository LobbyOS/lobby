<?php
/**
 * PHP Prerequisite Checker - Class PrereqCheck
 *
 * This is the abstract base class for all individual Check classes. It defines
 * the common interface for all implementing Check classes.
 *
 * Implementing Check classes must implement the check() function, which is
 * called with all arguments given in the PrereqChecker::checkMandatory()/checkOptional()
 * functions. Within check(), you can call $this->setSucceed() and $this->setFailed() to mark
 * the check.
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */
namespace Prereq;

abstract class PrereqCheck {
	public $name = "Insert check name here";
	protected $result;

	/**
	 * @param string $name Name of the check, used in the Output
	 */
	public function __construct($name = null) {
		if ($name) $this->name = $name;
		$this->result = new CheckResult(true,$this);
	}

	/**
	 * returns the associated ResultCheck object
	 * @return ResultCheck
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * Marks this check as passed successfully.
	 */
	public function setSucceed() {
		$this->result->setSucceed();
	}

	/**
	 * Marks this check as failed, and sets a failure message.
	 */
	public function setFailed($msg = '') {
		$this->result->setFailed($msg);
	}
    
    /**
     * Check function, called with all arguments delivered in checkMandadory/checkOptional.
     * Child classes must access function arguments with func_get_args(), or define
     * arguments with optional parameters to not break the signature of this function.
     */
    abstract public function check();
}
