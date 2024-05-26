<?php
use Carbon\Carbon;
class Animal implements JsonSerializable
{
    private string $name;
    private string $race;
    private array $bestFood;
    private int $happiness;
    private int $hungriness;
    private string $state;
    private int $stateStart;
    private int $current;

    public function __construct(
        string $name,
        string $race,
        array $bestFood,
        int $happiness = 50,
        int $hungriness = 80,
        string $state = 'idle'
        )
    {
        $this->name = $name;
        $this->race = $race;
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
        if($this->validateFood(readline("What are you going to feed: ")))
        {
            $this->addHappiness(10);
            $this->addHungriness(-20);
            return;
        }
        $this->addHungriness(10);
        $this->addHappiness(-10);
    }
    public function pet(): void
    {
        $this->addHappiness(10);
    }
    private function validateFood($food): bool
    {
        if(in_array($food, $this->bestFood)) {
            return true;
        }
        return false;
    }
    public function getFoodStr()
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
        $this->happiness += $happiness;
    }
    public function getHungriness(): int
    {
        return $this->hungriness;
    }
    public function addHungriness(int $hungriness): void
    {
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
}