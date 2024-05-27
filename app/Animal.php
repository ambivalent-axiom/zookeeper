<?php
namespace App;
use Carbon\Carbon;
use JsonSerializable;
class Animal implements JsonSerializable
{
    private string $name;
    private string $race;
    private ZooKeeper $keeper;
    private Zoo $zoo;
    private array $bestFood;
    private int $happiness;
    private int $hungriness;
    private string $state;
    private int $stateStart;

    public function __construct(
        string $name,
        string $race,
        ZooKeeper $keeper,
        Zoo $zoo,
        array  $bestFood,
        int    $happiness = 50,
        int    $hungriness = 80,
        string $state = 'idle'
    )
    {
        $this->name = $name;
        $this->race = $race;
        $this->keeper = $keeper;
        $this->zoo = $zoo;
        $this->bestFood = $bestFood;
        $this->hungriness = $hungriness;
        $this->happiness = $happiness;
        $this->state = $state;
        $this->stateStart = Carbon::now()->timestamp;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'race' => $this->race,
            'bestFood' => $this->bestFood,
            'hungriness' => $this->hungriness,
            'happiness' => $this->happiness,
        ];
    }
    public function feed(): void
    {

        if ($this->validateFood(Zoo::validateName('Food', "What are you going to feed: "))) {
            $this->addHappiness(10);
            $this->addHungriness(-20);
            $this->zoo->addToMessages(
                Zoo::message($this->getKeeper(),
                    'fed correct food',
                    $this->name,
                    'Happiness +' . 10 . ' Hunger -' . 20 . ' credit -' . 20
                )
            );
            return;
        }
        $this->addHungriness(10);
        $this->addHappiness(-10);
        $this->zoo->addToMessages(
            Zoo::message($this->getKeeper(),
                'fed incorrect food',
                $this->name,
                'Happiness -' . 10 . ' Hunger +' . 10 . ' credit -' . 20
            )
        );
    }
    public function pet(): void
    {
        $this->addHappiness(10);
        $this->zoo->addToMessages(
            Zoo::message($this->getKeeper(),
                'petted',
                $this->name,
                'Happiness +' . 10
            )
        );
    }
    private function validateFood($food): bool
    {
        if (in_array($food, $this->bestFood)) {
            return true;
        }
        return false;
    }
    public function getFoodStr(): string
    {
        return implode(', ', $this->bestFood);
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getRace(): string
    {
        return $this->race;
    }
    public function getHappiness(): int
    {
        return $this->happiness;
    }
    public function addHappiness(int $happiness): void
    {
        if ($this->happiness + $happiness > 100) {
            $this->happiness = 100;
        }
        $this->happiness += $happiness;
    }
    public function getHungriness(): int
    {
        return $this->hungriness;
    }
    public function addHungriness(int $hungriness): void
    {
        if (($this->hungriness + $hungriness) < 0) {
            $this->hungriness = 0;
        }
        $this->hungriness += $hungriness;
    }
    public function getState(): string
    {
        return $this->state;
    }
    public function setState(string $state): void
    {
        $this->state = $state;
    }
    public function setStateStart(int $stateStart): void
    {
        $this->stateStart = $stateStart;
    }
    public function getStateStart(): int
    {
        return $this->stateStart;
    }
    private function getKeeper(): string
    {
        return $this->keeper->getName();
    }
}