<?php
/**
 * Lobby\CLI
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/CLI.php
 */
namespace Lobby;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Line Interface for Lobby
 */
class CLI extends Application {
  
  /**
   * Configure
   */
  protected function configure(){
    $this->addArgument("command");
  }
  
  /**
   * Return command name based on input
   * @param InputInterface $input CLI input
   * @return string Command name
   */
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
