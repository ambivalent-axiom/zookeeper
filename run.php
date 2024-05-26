<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'app/classes/Zookeeper.php';
require_once 'app/classes/Zoo.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$application = new Application();
$playCommand = new class extends Command {
    protected static $defaultName = 'start';
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //init load
        if(file_exists('savedZoos/Pelikan/Pelikan.json'))
        {
            //load zoo
            $zoo = Zoo::loadZoo('savedZoos/Pelikan/Pelikan.json', $output, $input);
        } else {
            //Init new zoo
            $keeper = new Zookeeper(readline("Enter Zookeeper name: "));
            $zoo = new Zoo(readline("Name {$keeper->getName()}'s Zoo : "), $keeper, $output, $input);
        }
        return Command::SUCCESS;
    }
};
$application->add($playCommand);
$application->setDefaultCommand('start', true);
$application->run();