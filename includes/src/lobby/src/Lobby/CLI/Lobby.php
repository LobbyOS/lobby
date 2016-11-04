<?php
/**
 * Lobby\CLI\Lobby
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/CLI/Lobby.php
 */

namespace Lobby\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Lobby\Apps;

/**
 * CLI handler for `lobby` command
 */
class Lobby extends Command {

  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName("lobby")
      ->setDescription("Manage Lobby")
      ->setHelp("Manage your Lobby installation");
    $this->addOption("v", null, InputOption::VALUE_NONE, "Show version");

    $this->addOption("apps", null, InputOption::VALUE_NONE, "Show apps installed");
  }

  /**
   * Handle commands
   * @param InputInterface $input Interface to get input
   * @param OutputInterface $output Interface to output to console
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if($input->getOption("apps")){
      echo implode(PHP_EOL, Apps::getApps());
    }else if($input->getOption("v")){
      if($input->getOption("v") === "code")
        $output->writeln(\Lobby::getVersion());
      else
        $output->writeln(\Lobby::getVersion(true));
    }else{
      $output->writeln("Lobby version " . \Lobby::getVersion(true));
    }
  }

}
