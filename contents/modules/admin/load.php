<?php
if(\Lobby::curPage() != "/includes/serve.php"){
  require_once __DIR__ . "/install.php";
  require_once __DIR__ . "/module.php";
}
