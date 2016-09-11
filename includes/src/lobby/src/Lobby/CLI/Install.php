<?php
/**
 * Lobby\CLI\Install
 * @link https://github.com/LobbyOS/lobby/tree/dev/includes/src/lobby/src/Lobby/CLI/Install.php
 */

namespace Lobby\CLI;

use Lobby;
use Lobby\CLI;
use Lobby\FS;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * CLI handler for `lobby install` command
 */
class Install extends Command {

  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName("lobby install")
      ->setDescription("Install Lobby")
      ->setHelp("Helps you to install Lobby");

    /**
     * Database options
     */
    $this->addOption("db", null, InputOption::VALUE_OPTIONAL, "Database type", "sqlite");
    $this->addOption("prefix", null, InputOption::VALUE_OPTIONAL, "Prefix of table names going to be created in database", "l_");

    /**
     * SQLite options
     */
    $this->addOption("sqlite-path", null, InputOption::VALUE_OPTIONAL, "Absolute path to SQLite database", FS::loc("/contents/extra/lobby_db.sqlite"));

    /**
     * General options
     */
    $this->addOption("enable-ledit", null, InputOption::VALUE_OPTIONAL, "Should lEdit be enabled when Lobby is installed", true);
    $this->addOption("status", null, InputOption::VALUE_NONE, "Whether Lobby is installed or not");
  }

  /**
   * Handle commands
   * @param InputInterface $input Interface to get input
   * @param OutputInterface $output Interface to output to console
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if($input->getOption("status")){
      if(Lobby::$installed){
        $output->writeln("Lobby is installed");
      }else{
        $output->writeln("Lobby not installed");
      }
    }else{
      if(Lobby::$installed){
        $helper = $this->getHelper("question");
        $question = new ConfirmationQuestion("<error>Lobby is already installed. Should I re-install</error> ? ", false);

        if(!$helper->ask($input, $output, $question)) {
          $output->writeln("Installation cancelled");
          return;
        }
      }

      $db = $input->getOption("db");
      if($db === "mysql"){

      }else if($db === "sqlite"){
        Lobby\Install::createSQLiteDB($input->getOption("sqlite-path"));
        Lobby\Install::makeDatabase($input->getOption("prefix"), "sqlite");

        Lobby\Install::dbConfig(array(
          "path" => FS::rel($input->getOption("sqlite-path")),
          "prefix" => $input->getOption("prefix")
        ));

        /**
         * Make the Config File
         */
        Lobby\Install::makeConfigFile("sqlite");

        chgrp($input->getOption("sqlite-path"), "www-data");
      }

      chgrp(FS::loc("/config.php"), "www-data");

      Lobby::$installed = true;
      Lobby\DB::__constructStatic();

      if($input->getOption("enable-ledit")){
        /**
         * Enable app lEdit
         */
        $App = new Lobby\Apps("ledit");
        $App->enableApp();
      }

      $output->writeln(array(
        "<comment>Installation summary</comment>",
        "Database type => $db",
        "Location of config.php => " . FS::loc("/config.php")
      ));
      $output->writeln("<info>Lobby installed successfully</info>");
    }
  }

}
