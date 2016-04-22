<?php
/**
 * PHP Prerequisite Checker - DB PDO Connection Check
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */
namespace Prereq;

class DbPdoConnectionPrereqCheck extends PrereqCheck {
    public $_name = 'DB PDO Connection';

    /**
     * @param array $options An array with the needed DB parameters. Must contain:
     *   - dsn: A PDO dsn for the connection
     *   - username: A username to be used for the connection
     *   - password: The connection password
     */
    public function check($options = array()) {
    	$this->name = $this->_name . ': DSN: '.$options['dsn'];
    	try {
    		$ret = $this->pdoConnect($options['dsn'],$options['username'],$options['password']);
    		if (!$ret) {
    			$this->setFailed('Connection could not be established.');
    		}
    	} catch (\PDOException $e) {
    		$this->setFailed('Connection could not be established.');
    	}
    }

    protected function pdoConnect($dsn, $user, $pw) {
    	$pdo = new \PDO($dsn,$user,$pw);
    	if ($pdo) return true;
    	return false;
    }
}
