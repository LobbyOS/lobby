<?php
namespace Lobby;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CLI extends Application {

  private static $cli;
  
  protected function configure(){
    $this->addArgument("command");
  }
  
  protected function getCommandName(InputInterface $input) {
    if($input->getFirstArgument())
      return "lobby " . $input->getFirstArgument();
    return "lobby";
  }
  
  /**
   * Gets the default commands that should always be available.
   *
   * @return array An array of default Command instances
   */
  protected function getDefaultCommands(){
    // Keep the core default commands to have the HelpCommand
    // which is used when using the --help option
    $defaultCommands = parent::getDefaultCommands();

    $defaultCommands[] = new CLI\App();
    $defaultCommands[] = new CLI\Lobby();

    return $defaultCommands;
  }

}
