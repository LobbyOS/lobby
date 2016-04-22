<?php
/**
 * PHP Prerequisite Checker - Autoloader
 *
 * (c) 2014 Alexander Schenkel, info@alexi.ch
 */

namespace Prereq;
require_once(__DIR__.'/src/PrereqChecker.php');
spl_autoload_register(__NAMESPACE__.'\PrereqChecker::autoload');
