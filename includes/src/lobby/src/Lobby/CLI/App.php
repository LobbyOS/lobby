<?php
/**
 * Lobby\CLI\App
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/CLI/App.php
 */

namespace Lobby\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * CLI handler for `lobby app` command
 */
class App extends Command {

  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName("lobby app")
      ->setDescription("Control an app")
      ->setHelp("Helps you to manage an app");

    $this->addOption("a", null, InputOption::VALUE_REQUIRED, "The App ID", 0);
    $this->addOption("i", null, InputOption::VALUE_OPTIONAL, "Path to file in app to include", null);
    $this->addOption("data", null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "Parameters to pass to script", array());
  }

  /**
   * Handle commands
   * @param InputInterface $input Interface to get input
   * @param OutputInterface $output Interface to output to console
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $appID = $input->getOption("a");

    $App = new \Lobby\Apps($appID);
    if(!$App->exists){
      $output->writeln("<fg=red>App doesn't exist</>");
      exit;
    }

    $AppClass = $App->getInstance();

    $data = array();
    if($input->getOption("data")){
      foreach($input->getOption("data") as $v){
        list($key, $value) = explode("=", $v);
        $data[$key] = $value;
      }
    }

    if($input->getOption("i"))
      echo $AppClass->inc($input->getOption("i"), $data);
  }

}
