<?php
/**
 * Lobby\CLI\Notify
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/CLI/Notify.php
 */

namespace Lobby\CLI;

use Lobby\DB;
use Lobby\FS;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI handler for `lobby notify` command
 */
class Notify extends Command {

  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName("lobby notify")
      ->setDescription("Manage Lobby notifications")
      ->setHelp("Handle notifications in Lobby");

    $this->addOption("count", null, InputOption::VALUE_NONE, "Get the number of notifications");
    $this->addOption("clear", null, InputOption::VALUE_NONE, "Clear notifications");
  }

  /**
   * Handle commands
   * @param InputInterface $input Interface to get input
   * @param OutputInterface $output Interface to output to console
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if($input->getOption("count")){
      /**
       * Suppress the JSON data printing
       */
      ob_start();
        require FS::loc("includes/lib/lobby/ajax/notify.php");
      ob_end_clean();

      $output->writeln(count($notifications));
    }else if($input->getOption("clear")){
      DB::saveJSONOption("notify_items", false);
      $output->writeln("<info>Notifications cleared</info>");
    }else{
      $output->writeln("See lobby notify -h");
    }
  }

}
