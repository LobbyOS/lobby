<?php
use Neutron\TemporaryFilesystem\TemporaryFilesystem;

$GLOBALS["FS"] = TemporaryFilesystem::create();

define("WEB_SERVER_DOCROOT", $GLOBALS["FS"]->createTemporaryDirectory($mode = 0755));

exec("cp -R '". realpath(__DIR__ . "/../") . "/.' '". WEB_SERVER_DOCROOT ."'");

echo "Mocking filesytem at " . WEB_SERVER_DOCROOT . PHP_EOL;

unlink(WEB_SERVER_DOCROOT . "/config.php");

/**
 * Start Server
 */
$command = sprintf(
    'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    WEB_SERVER_DOCROOT
);

// Execute the command and store the process ID
$output = array(); 
exec($command, $output);
$pid = (int) $output[0];
 
echo sprintf(
    '%s - Web server started on %s:%d with PID %d', 
    date('r'),
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    $pid
) . PHP_EOL;
 
// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
    exec('kill ' . $pid);
});
