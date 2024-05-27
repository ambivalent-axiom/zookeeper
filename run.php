<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'app/classes/Zookeeper.php';
require_once 'app/classes/Zoo.php';
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

$application = new Application();
$playCommand = new class extends Command {
    protected static $defaultName = 'start';
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //init load
        $options = ['new Zoo'];
        if(file_exists('savedZoos/')) {
            $contents = scandir('savedZoos/');
            foreach ($contents as $content) {
                if ($content != '.' && $content != '..') {
                    $options[] = $content;
                }
            }
        }
        $choice = new ChoiceQuestion('Select Your Zoo?', $options);
        $helper = new QuestionHelper();
        $choice->setErrorMessage('Option %s is invalid.');
        $choice = $helper->ask($input, $output, $choice);

        if($choice === 'new Zoo') {
            //Init new zoo
            $keeper = new Zookeeper(readline("Enter Zookeeper name: "));
            $zoo = new Zoo(readline("Name {$keeper->getName()}'s Zoo : "), $keeper, $output, $input);
        } else {
            //load zoo
            $address = 'savedZoos/' . $choice . '/' . $choice . '.json';
            $zoo = Zoo::loadZoo($address, $output, $input);
        }
        return Command::SUCCESS;
    }
};
$application->add($playCommand);
$application->setDefaultCommand('start', true);
$application->run();