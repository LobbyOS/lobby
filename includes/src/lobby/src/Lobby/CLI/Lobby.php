<?php
namespace Lobby\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Lobby extends Command {

  protected function configure() {
    $this->setName("lobby")
      ->setDescription("Control an app")
      ->setHelp("Helps you to manage an app");
    $this->addOption("v", null, InputOption::VALUE_OPTIONAL, "Show version", 1);
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    if($input->getOption("v")){
      if($input->getOption("v") === "code")
        $output->writeln(\Lobby::getVersion());
      else
        $output->writeln(\Lobby::getVersion(true));
    }
  }

}
