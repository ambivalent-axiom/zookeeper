<?php

require_once 'Animal.php';
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
class Zoo implements JsonSerializable
{
    private string $name;
    private Zookeeper $keeper;
    private int $funds;
    private array $animals;
    private OutputInterface $symfonyOutput;

    public function __construct(string $name, Zookeeper $keeper, $symfonyOutput, array $animals=[], int $funds=100)
    {
            $this->name = $name;
            $this->keeper = $keeper;
            $this->animals = $animals;
            $this->funds = $funds;
            $this->symfonyOutput = $symfonyOutput;
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

    public function showZoo(): void
    {
        foreach ($this->animals as $animal) {
            echo $animal->getName() . " " .
                $animal->getRace() . " " .
                $animal->getHappiness() . " " .
                $animal->getHungriness(), PHP_EOL;
        }
    }
    public function addAnimal(string $name, string $race, array $bestFood): void
    {
        $this->animals[] = new Animal($name, $race, $bestFood);
    }
    public function saveZoo(): void
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
    public static function loadZoo(string $json, OutputInterface $output): Zoo
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
        return new self($zoo->name, $keeper, $output, $animals, $zoo->funds);
    }
    public function mainLoop(): void
    {
        while(true) {
            $this->showZoo();
            echo "--------------------------------------------------------\n";
            echo "1. add animal.\n";
            echo "2. refresh zoo.\n";
            echo "3. save Zoo.\n";
            echo "9. exit.\n";

            switch (readline("Enter choice: "))
            {
                case 1:
                    $name = (string) readline("Enter name: ");
                    $race = (string) readline("Enter Race: ");
                    $bestFood = (array) explode(" ", readline("Enter Best food: "));
                    $this->addAnimal($name, $race, $bestFood);
                    break;
                case 2:
                    break;
                case 3:
                    try {
                        $this->saveZoo();
                    } catch(Exception $e) {
                        echo "Error: " . $e->getMessage() . "\n";
                    }

                    break;
                case 9:
                    exit;
            }
        }
    }
}