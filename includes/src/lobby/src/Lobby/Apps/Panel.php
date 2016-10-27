<?php
namespace Lobby\Apps;

use Lobby\App;
use Lobby\UI\Panel as LobbyPanel;

/**
 * Manage Panel in app
 */
class Panel {

  /**
   * @var Lobby\App Object of app running this
   */
  private $app;

  /**
   * @var string App ID
   */
  private $appID;

  public function __construct(App $App){
    $this->app = $App;
    $this->appID = $App->appID;
  }

  /**
   * @see \Lobby\UI\Panel::addTopItem()
   */
  public function addTopItem($id, $info){
    $this->changeToAppRelativeURLs($info);
    return LobbyPanel::addTopItem("app-{$this->appID}-$id", $info);
  }

  /**
   * @see \Lobby\UI\Panel::removeTopItem()
   */
  public function removeTopItem($id, $position){
    return LobbyPanel::removeTopItem("app-{$this->appID}-$id", $position);
  }

  /**
   * @see \Lobby\UI\Panel::getTopItems()
   */
  public function getTopItems($position){
    return LobbyPanel::getTopItems($position);
  }

  /**
   * @see \Lobby\UI\Panel::addLeftItem()
   */
  public function addLeftItem($id, $info){
    $this->changeToAppRelativeURLs($info);
    return LobbyPanel::addLeftItem("app-{$this->appID}-$id", $info);
  }

  /**
   * @see \Lobby\UI\Panel::removeLeftItem()
   */
  public function removeLeftItem($id, $position){
    return LobbyPanel::removeLeftItem("app-{$this->appID}-$id", $position);
  }

  /**
   * @see \Lobby\UI\Panel::getLeftItems()
   */
  public function getLeftItems($position){
    return LobbyPanel::getLeftItems($position);
  }

  /**
   * @see \Lobby\UI\Panel::addNotifyItem()
   */
  public function addNotifyItem($id, $info){
    return LobbyPanel::addNotifyItem("app-{$this->appID}-$id", $info);
  }

  /**
   * @see \Lobby\UI\Panel::removeNotifyItem()
   */
  public function removeNotifyItem($id){
    return LobbyPanel::removeNotifyItem("app-{$this->appID}-$id");
  }

  public function changeToAppRelativeURLs(&$array){
    $App = $this->app;
    array_walk_recursive($array, function(&$value, $key) use($App){
      if($key === "href")
        $value = $App->u($value);
    });
  }

}
