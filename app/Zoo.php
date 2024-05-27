<?php
namespace App;
use Exception;
use JsonSerializable;
use Carbon\Carbon;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Zoo implements JsonSerializable
{
    private string $name;
    private ZooKeeper $keeper;
    private int $funds;
    private array $animals;
    private array $message;
    private OutputInterface $symfonyOutput;
    private InputInterface $symfonyInput;
    private QuestionHelper $helper;

    const FEEDING_COST = 20;
    const ANIMAL_COST = 100;
    public function __construct(
        string $name,
        ZooKeeper $keeper,
        OutputInterface $symfonyOutput,
        InputInterface $symfonyInput,
        array $animals=[],
        int $funds=1000)
    {
            $this->name = $name;
            $this->keeper = $keeper;
            $this->animals = $animals;
            $this->funds = $funds;
            $this->message = [];
            $this->symfonyOutput = $symfonyOutput;
            $this->symfonyInput = $symfonyInput;
            $this->helper = new QuestionHelper();
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
        $table->setFooterTitle("ZooKeeper: " . $this->keeper->getName());
        $table->setStyle('box-double');
        $table->render();
    }
    private function addAnimal(string $name, string $race, array $bestFood): void
    {
        $this->animals[] = new Animal($name, $race, $this->keeper, $this, $bestFood);
        $this->addFunds(-100);
        $this->message[] = $this->message(
            $this->keeper->getName(),
            'bought for',
            100 . 'credits',
            $this->getName(),
            $name . " the " . $race . "."
        );
    }
    private function saveZoo(): void
    {
        $zoo = 'savedZoos/' . strtolower($this->name) . '/' . strtolower($this->name) . '.json';
        $path = 'savedZoos/' . strtolower($this->name) . '/';
        if( ! file_exists($zoo))
        {
            mkdir($path, 0777, true);
        }
        $json = json_encode($this);
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
            'add animal',
            'feed animal',
            'pet animal',
            'send animal to work',
            'send animal to play',
            'remove animal',
            'refresh Zoo',
            'save Zoo',
            'exit'
        ];
        while(true) {
            $this->cls();
            $this->stateCron();
            $this->removeDeadAnimals();
            $this->showZoo();
            $this->printMessages();
            $this->clearMessages();
            $choice = new ChoiceQuestion('What would you like to do?', $options);
            $choice->setErrorMessage('Option %s is invalid.');
            $choice = $this->helper->ask($this->symfonyInput, $this->symfonyOutput, $choice);
            switch ($choice)
            {
                case 'add animal':
                    echo "Cost of action:" . self::ANIMAL_COST . 'c' . "\n";
                    $name = self::validateName('Name', "Enter name: ");
                    $race = self::validateName('Race', "Enter race: ");
                    $bestFood = (array) explode(" ", readline("Enter Best food, separate by space: "));
                    $this->addAnimal($name, $race, $bestFood);
                    break;
                case 'feed animal':
                    echo "Cost of action: " . self::FEEDING_COST . 'c' . "\n";
                    $animal = $this->selectAnimals();
                    $animal->feed();
                    $this->addFunds(-self::FEEDING_COST);
                    break;
                case 'pet animal':
                    $animal = $this->selectAnimals();
                    $animal->pet();
                    break;
                case 'refresh Zoo':
                    break;
                case 'save Zoo':
                    try {
                        $this->saveZoo();
                    } catch(Exception $e) {
                        echo "Error: " . $e->getMessage() . "\n";
                    }
                    break;
                case 'send animal to play':
                    $animal = $this->selectAnimals();
                    $animal->setState('playing');
                    $animal->setStateStart(Carbon::now()->timestamp);
                    break;
                case 'send animal to work':
                    $animal = $this->selectAnimals();
                    $animal->setState('working');
                    $animal->setStateStart(Carbon::now()->timestamp);
                    break;
                case 'remove animal':
                    $animal = $this->selectAnimals();
                    $this->removeAnimal($animal);
                    break;
                case 'exit':
                    exit;
            }
        }
    }
    private function selectAnimals(): Animal
    {   $this->cls();
        $this->showZoo();
        $options = array_map(function ($animal) {
            return strtolower($animal->getName());
        }, $this->animals);
        $choice = new ChoiceQuestion('Choose an animal to interact?', $options);
        $choice->setErrorMessage('Option %s is invalid.');
        $choice = $this->helper->ask($this->symfonyInput, $this->symfonyOutput, $choice);
        $index = array_search($choice, $options);
        return $this->animals[$index];
    }
    private function addFunds(int $funds): void
    {
        $this->funds += $funds;
    }
    public static function cls(): void {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }
    private function stateCron()
    {
        foreach ($this->animals as $animal) {
            if  ($animal->getState() == 'playing') {
                $timeTrack = Carbon::now()->timestamp - $animal->getStateStart();
                if($timeTrack > 0) {
                    $remainder = $timeTrack%5;
                    $period = $timeTrack - $remainder;
                    $chargeFor = $period/5;
                    $animal->setStateStart(Carbon::now()->timestamp+$remainder);
                    $animal->addHungriness($chargeFor);
                    $animal->addHappiness($chargeFor);
                    $this->message[] = $this->message($animal->getName(),
                        'played',
                        'for ' . $period . 'sec.',
                        'Happiness +' . $chargeFor . ' Hunger +' . $chargeFor
                    );
                }
            }
            if  ($animal->getState() == 'working') {
                $timeTrack = Carbon::now()->timestamp - $animal->getStateStart();
                if($timeTrack > 0) {
                    $remainder = $timeTrack%5;
                    $period = $timeTrack - $remainder;
                    $chargeFor = $period/5;
                    $animal->setStateStart(Carbon::now()->timestamp+$remainder);
                    $animal->addHungriness($chargeFor);
                    $animal->addHappiness(-$chargeFor);
                    $this->addFunds($chargeFor);
                    $this->message[] = $this->message($animal->getName(),
                        'worked',
                        'for ' . $period . 'sec.',
                        'Happiness -' . $chargeFor . ' Hunger +' . $chargeFor,
                        $this->getName() . " credits +" . $chargeFor
                    );
                }
            }
        }
    }
    private function removeDeadAnimals(): void {
        foreach ($this->animals as $animal) {
            if ($animal->getHungriness() >= 100) {
                $position = array_search($animal, $this->animals);
                $this->message[] = $this->message($animal->getName(),
                    'died',
                    'due to hunger.',
                    'Removed from ' . $this->getName(),
                    $animal->getName()
                );
                unset($this->animals[$position]);

            }
            if ($animal->getHappiness() <= 0) {
                $position = array_search($animal, $this->animals);
                $this->message[] = $this->message($animal->getName(),
                    'died',
                    'due to being very upset.',
                    'Removed from ' . $this->getName(),
                    $animal->getName()
                );
                unset($this->animals[$position]);
            }
        }
    }
    private function removeAnimal($animal): void
    {
        $index = array_search($animal, $this->animals);
        $this->message[] = $this->message(
            $this->keeper->getName(),
            'removed',
            $animal->getName(),
            'from ' . $this->getName()
        );
        unset($this->animals[$index]);
    }
    public static function message(
        string $who,
        string $action,
        string $how,
        string $result,
        string $what = ''): string {
        return Carbon::now()->toDateTimeString() .
            " " .
            $who . " " .
            $action . " " .
            $how . " " .
            $result . " " .
            $what;
    }
    public function getName(): string
    {
        return $this->name;
    }
    private function printMessages() {
        foreach ($this->message as $message) {
            echo $message . "\n";
        }
    }
    private function clearMessages(): void
    {
        $this->message = [];
    }
    public function addToMessages(string $message): void
    {
        $this->message[] = $message;
    }
    public static function validateName(string $who, string $prompt): string
    {
        while(true) {
            $name = readline($prompt);
            if($name != '' && strlen($name) <= 12 && !is_numeric($name)) {
                return $name;
            }
            echo "$who name must be a string, max 12 chars.\n";
        }
    }
}