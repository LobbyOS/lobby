<?php
namespace Lobby;

use Lobby\FS;

/**
 * Lobby\FS in Object context
 */

class FSObj {
  
  private $base = null;
  
  public function __construct($base = null){
    $this->base = FS::rel($base);
  }
  
  /**
   * Map static functions into object methods
   */
  public function __call($function, $args){
    switch($function){
      case "exists":
      case "loc":
        return FS::$function($this->base . "/" . $args[0]);
      case "get":
        return FS::get($this->base . "/" . $args[0]);
      case "write":
      case "remove":
        return call_user_func_array(FS::$function, array(
          $this->base . "/" . $args[0], $args[1], $args[2]
        ));
    }
  }

}
