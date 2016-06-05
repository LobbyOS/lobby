<?php
require "../load.php";
unset($_SESSION['checkedForLatestVersion']);
\Lobby::$serverCheck = true;
require L_DIR . "/includes/init.php";
Response::redirect("/admin/update.php");
?>
