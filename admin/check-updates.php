<?php
require "../load.php";
unset($_SESSION['checkedForLatestVersion']);
\Lobby::$serverCheck = true;
require L_DIR . "/includes/init.php";
\Lobby::redirect("/admin/update.php");
?>
