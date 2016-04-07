<?php

/**
 * PHP Prerequisite Checker - Checks if specified Dir is writable
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */
namespace Prereq;

class DirWritablePrereqCheck extends PrereqCheck {
    private $_name = 'Dir writable: ';

    /**
     * @param string $dir Path to a dir to check
     */
    public function check($dir = null) {
        $this->name = $this->_name . $dir;

        if (!is_dir($dir) || !is_writable($dir)) {
            $this->setFailed("Directory '{$dir}' not writable.");
        }
    }
}
