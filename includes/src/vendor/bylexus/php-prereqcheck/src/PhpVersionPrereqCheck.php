<?php
/**
 * PHP Prerequisite Checker - PHP Version Check
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */
namespace Prereq;

class PhpVersionPrereqCheck extends PrereqCheck {
    public $_name = 'PHP Version Check';

    /**
     * @param string $operator A comparator operator, e.g. '>='
     * @param string $requiredVersion The required PHP version
     */
    public function check($operator = '>=', $requiredVersion = '5.3.0') {
        $actualVersion = phpversion();
        $this->name = $this->_name . "({$operator} {$requiredVersion})";

        if (version_compare ( $actualVersion, $requiredVersion, $operator) !== true) {
            $this->setFailed("Actual PHP Version ({$actualVersion}) does not meet the requirement {$operator} {$requiredVersion}");
        }
    }
}
