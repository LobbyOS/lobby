<?php
require_once __DIR__ . "/vendor/autoload.php";

if(file_exists(sys_get_temp_dir() . "/lobby-tmp-loc.txt")){
  define("WEB_SERVER_DOCROOT", file_get_contents(sys_get_temp_dir() . "/lobby-tmp-loc.txt"));
}else{
  die("Run 'setup-tests.php' first before starting tests.\n");
}

echo "Lobby installed at " . WEB_SERVER_DOCROOT . PHP_EOL;

/**
 * Start Server
 */
$command = sprintf(
    'php -S %s:%d -t '. WEB_SERVER_DOCROOT .' "'. WEB_SERVER_DOCROOT .'/index.php" >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT
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

echo "Command used to start server : " . $command . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
    exec('kill ' . $pid);
});
