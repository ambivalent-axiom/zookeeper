<?php
require_once 'Animal.php';
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;
class Zoo implements JsonSerializable
{
    private string $name;
    private Zookeeper $keeper;
    private int $funds;
    private array $animals;
    private OutputInterface $symfonyOutput;
    private InputInterface $symfonyInput;
    public function __construct(string $name, Zookeeper $keeper, $symfonyOutput, $symfonyInput, array $animals=[], int $funds=1000)
    {
            $this->name = $name;
            $this->keeper = $keeper;
            $this->animals = $animals;
            $this->funds = $funds;
            $this->symfonyOutput = $symfonyOutput;
            $this->symfonyInput = $symfonyInput;
            $this->mainLoop();
    }
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'keeper' => $this->keeper,
            'funds' => $this->funds,
            'animals' => $this->animals,
        ];
    }
    private function showZoo(): void
    {
        $table = new Table($this->symfonyOutput);
        $table
            ->setHeaders(['Name', 'Race', 'Happiness', 'Hungriness', 'Eats', 'Current state'])
            ->setRows(array_map(function ($animal) {
                return [
                    $animal->getName(),
                    $animal->getRace(),
                    $animal->getHappiness(),
                    $animal->getHungriness(),
                    $animal->getFoodStr(),
                    $animal->getState(),
                ];
            }, $this->animals));
        $table->setHeaderTitle($this->name . " Credits: " . $this->funds);
        $table->setFooterTitle("Zookeeper: " . $this->keeper->getName());
        $table->setStyle('box-double');
        $table->render();
    }
    private function addAnimal(string $name, string $race, array $bestFood): void
    {
        $this->animals[] = new Animal($name, $race, $bestFood);
    }
    private function saveZoo(): void
    {
        $zoo = 'savedZoos/' . $this->name . '/' . $this->name . '.json';
        $path = 'savedZoos/' . $this->name . '/';
        if( ! file_exists($zoo))
        {
            mkdir($path, 0777, true);
        }
        $json = json_encode($this);
        if ($json === false) {
            throw new Exception("JSON encoding failed: " . json_last_error_msg());
        }
        file_put_contents($zoo, $json);
    }
    public static function loadZoo(string $json, OutputInterface $output, InputInterface $input): Zoo
    {
        $zoo = json_decode(file_get_contents($json));
        $keeper = new ZooKeeper($zoo->keeper->name);
        $animals = [];

        foreach ($zoo->animals as $animal) {
            $animals[] = new Animal(
                $animal->name,
                $animal->race,
                $animal->bestFood,
                $animal->hungriness,
                $animal->happiness
            );
        }
        return new self($zoo->name, $keeper, $output, $input, $animals, $zoo->funds);
    }
    private function mainLoop(): void
    {
        $options = [
            'Add animal',
            'Feed animal',
            'Play with animal',
            'Send animal to work',
            'Send animal to play',
            'Refresh Zoo',
            'Save Zoo',
            'Exit'
        ];
        while(true) {
            $this->cls();
            $this->showZoo();
            $helper = new QuestionHelper();
            $choice = new ChoiceQuestion('What would you like to do?', $options);
            $choice->setErrorMessage('Option %s is invalid.');
            $choice = $helper->ask($this->symfonyInput, $this->symfonyOutput, $choice);
            switch ($choice)
            {
                case 'Add animal':
                    $name = (string) readline("Enter name: ");
                    $race = (string) readline("Enter Race: ");
                    $bestFood = (array) explode(" ", readline("Enter Best food: "));
                    $this->addAnimal($name, $race, $bestFood);
                    break;
                case 'Refresh Zoo':
                    break;
                case 'Save Zoo':
                    try {
                        $this->saveZoo();
                    } catch(Exception $e) {
                        echo "Error: " . $e->getMessage() . "\n";
                    }
                    break;
                case 'Exit':
                    exit;
            }
        }
    }
    private function cls(): void {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }
}