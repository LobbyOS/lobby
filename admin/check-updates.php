<?php
require __DIR__ . "/../load.php";
unset($_SESSION['checkedForLatestVersion']);

\Lobby\Server::check();
\Response::redirect("/admin/update.php");
?>
