<?php
class Animal implements JsonSerializable
{
    protected string $name;
    protected string $race;
    protected array $bestFood;
    protected int $happiness;
    protected int $hungriness;
    public function __construct(
        string $name,
        string $race,
        array $bestFood,
        int $happiness = 50,
        int $hungriness = 80
        )
    {
        $this->name = $name;
        $this->race = $race;
        $this->bestFood = $bestFood;
        $this->hungriness = $hungriness;
        $this->happiness = $happiness;
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

    protected function play(): void
    {
        echo "happy ++";
        echo "food --";
    }
    protected function work(): void
    {
        echo "happy --";
        echo "food --";
        echo "zoo money ++";
    }
    protected function validateFood(): void //should probably return bool
    {
        if(in_array('food', $this->bestFood)) {
            echo "food++ and happy ++";
        } else {
            echo "food---- and happy --";
        }
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
    public function getHungriness(): int
    {
        return $this->hungriness;
    }
}