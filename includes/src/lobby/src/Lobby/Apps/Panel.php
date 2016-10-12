<?php
namespace Lobby\Apps;

use Klein\Klein;
use Klein\Request;
use Lobby\App;
use Response;

class Panel {

  private $app;
  private $router;

  public function __construct(App $App){
    $this->app = $App;
    $this->router = new Klein();
  }

}
