<?php
require_once __DIR__ . "/vendor/autoload.php";

use Neutron\TemporaryFilesystem\TemporaryFilesystem;

$FS = TemporaryFilesystem::create();
define("WEB_SERVER_DOCROOT", $FS->createTemporaryDirectory($mode = 0755));

echo "Making the lobby web server docroot \n";

exec("cp -R '". realpath(__DIR__ . "/../") . "/.' '". WEB_SERVER_DOCROOT ."'");
unlink(WEB_SERVER_DOCROOT . "/config.php");
file_put_contents(sys_get_temp_dir() . "/lobby-tmp-loc.txt", WEB_SERVER_DOCROOT);
?>
