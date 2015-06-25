<?php
require "../load.php";
unset($_SESSION['checkedForLatestVersion']);
\Lobby::$serverCheck = true;
require \Lobby\FS::loc("/includes/init.php");
\Lobby::redirect("/admin/update.php");
?>