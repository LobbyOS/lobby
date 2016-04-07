<?php
/**
 * PHP Prerequisite Checker PHP ini value checker
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */
namespace Prereq;

class PhpIniPrereqCheck extends PrereqCheck {
    private $_name = 'PHP Setting: ';


    /**
     * @param string $param Name of the PHP ini directive, e.g. 'display_errors'
     * @param mixed $compareValue The value to compare against, e.g. 'Off'
     * @param string $type The ini directive type, e.g. 'string', 'boolean', 'number'
     */
    public function check($param = null, $compareValue = null, $type = 'string') {
        $this->name = $this->_name . $param;

        $iniValue = ini_get($param);
        switch (strtolower($type)) {
            case 'bool':
            case 'boolean':
                $this->booleanCompare($iniValue,$compareValue); break;
            case 'bit_disabled':
                $this->bitDisabledCompare($iniValue,$compareValue); break;
            case 'bit_enabled':
                $this->bitEnabledCompare($iniValue,$compareValue); break;
            case 'number':
                $this->numberCompare($iniValue,$compareValue); break;
            default:
                $this->stringCompare($iniValue, $compareValue);
        }
    }

    private function stringCompare($iniValue, $compareValue) {
        if (preg_match('/^\/.*\/$/',$compareValue)) {
            return $this->pregCompare($iniValue,$compareValue);
        } else if ($iniValue !== $compareValue) {
            $this->setFailed("ini value '{$iniValue}' does not match expected: '{$compareValue}'");
        }
    }

    private function pregCompare($iniValue,$compareValue) {
        if (!preg_match($compareValue,$iniValue)) {
            $this->setFailed("ini value '{$iniValue}' does not match regular expression: '{$compareValue}'");
        }
    }

    private function booleanCompare($iniValue, $compareValue) {
        $passed = false;
        $iniValue = $this->toBool($iniValue);
        $boolValue = $this->toBool($compareValue);

        if ($iniValue !== $boolValue) {
            $this->setFailed("ini value '{$iniValue}' does not match expected: '{$compareValue}'");
        }
    }

    private function bitDisabledCompare($iniValue, $compareValue) {
        $compareValue = (int)$compareValue;
        $iniValue = (int)$iniValue;
        if (($iniValue & $compareValue) === $compareValue) {
            $this->setFailed("bitfield value '{$compareValue}' is enabled, should not.");
        }
    }

    private function bitEnabledCompare($iniValue, $compareValue) {
        $compareValue = (int)$compareValue;
        $iniValue = (int)$iniValue;

        if (($iniValue & $compareValue) !== $compareValue) {
            $this->setFailed("bitfield value '{$compareValue}' is disabled, should be enabled.");
        }
    }

    private function numberCompare($iniValue, $compareValue) {
        $matches = array();
        $compareValue = preg_replace('/\s+/', '', $compareValue);
        preg_match('/^([<>=]*)([0-9.-]+)([a-zA-Z]*)$/', $compareValue, $matches);
        if (count($matches) !== 4) {
            throw new Exception('Error in Size definition.');
        }

        $operator = '=';
        if ($matches[1]) $operator = $matches[1];

        $number = $matches[2].$matches[3];
        if ($iniValue < 0) {
            $iniBytes = PHP_INT_MAX;
        } else {
            $iniBytes = $this->sizeStrToBytes($iniValue);    
        }
        
        $numberBytes = $this->sizeStrToBytes($number);
        $passed = false;
        switch ($operator) {
            case '>':
                if ($iniBytes > $numberBytes ) $passed = true; break;
            case '>=':
                if ($iniBytes >= $numberBytes ) $passed = true; ;break;
            case '<':
                if ($iniBytes < $numberBytes ) $passed = true; break;
            case '<=':
                if ($iniBytes <= $numberBytes ) $passed = true; break;
            case '=':
            default:
                if ($iniBytes !== $numberBytes ) $passed = true; break;
        }
        if (!$passed) {
            $this->setFailed("Ini Size '{$iniValue}' does not match the criteria '{$operator} {$number}'");
        }
    }

    private function sizeStrToBytes($sizeStr) {
        $matches = array();
        preg_match('/^([0-9.-]+)([a-zA-Z]+)$/', $sizeStr,$matches);
        $number = $matches[1];
        $exponent = strtolower($matches[2]);
        switch($exponent) {
            case 't':
            case 'tb': $number = $number * 1024;
            case 'g':
            case 'gb': $number = $number * 1024;
            case 'm':
            case 'mb': $number = $number * 1024;
            case 'k':
            case 'kb': $number = $number * 1024;
            default: $number = $number * 1.0;
        }
        return $number;
    }

    private function toBool($value) {
        $boolValues = array(true,'true',1,'1','on','yes');
        if (is_string($value)) {
            $value = strtolower($value);
        }
        foreach($boolValues as $compare) {
            if ($compare === $value) return true;
        }
        return false;
    }
}
