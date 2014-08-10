<?php
session_start();
session_destroy();
require "../load.php";
header("Location: ".L_HOST."/admin/upgrade.php");
?>