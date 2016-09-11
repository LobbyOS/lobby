#!/usr/bin/php
<?php
/**
 * Lobby CLI
 */

require_once __DIR__ . "/load.php";

$application = new Lobby\CLI();
$application->run();
