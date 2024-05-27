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
    const SMALL_CHARGE = 10;
    const MEDIUM_CHARGE = 20;
    const MAX = 100;
    const MIN = 0;
    const DEFAULT_STARTER = 50;

    public function __construct(
        string $name,
        string $race,
        ZooKeeper $keeper,
        Zoo $zoo,
        array  $bestFood,
        int    $happiness = self::DEFAULT_STARTER,
        int    $hungriness = self::DEFAULT_STARTER,
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
            $this->addHappiness(self::SMALL_CHARGE);
            $this->addHungriness(-self::MEDIUM_CHARGE);
            $this->zoo->addToMessages(
                Zoo::message($this->getKeeper(),
                    'fed correct food',
                    $this->name,
                    'Happiness +' . self::SMALL_CHARGE . ' Hunger -' . self::MEDIUM_CHARGE . ' credit -' . self::MEDIUM_CHARGE
                )
            );
            return;
        }
        $this->addHungriness(self::SMALL_CHARGE);
        $this->addHappiness(-self::SMALL_CHARGE);
        $this->zoo->addToMessages(
            Zoo::message($this->getKeeper(),
                'fed incorrect food',
                $this->name,
                'Happiness -' . self::SMALL_CHARGE . ' Hunger +' . self::SMALL_CHARGE . ' credit -' . self::MEDIUM_CHARGE
            )
        );
    }
    public function pet(): void
    {
        $this->addHappiness(self::SMALL_CHARGE);
        $this->zoo->addToMessages(
            Zoo::message($this->getKeeper(),
                'petted',
                $this->name,
                'Happiness +' . self::SMALL_CHARGE
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
        if ($this->happiness + $happiness > self::MAX) {
            $this->happiness = self::MAX;
        }
        $this->happiness += $happiness;
    }
    public function getHungriness(): int
    {
        return $this->hungriness;
    }
    public function addHungriness(int $hungriness): void
    {
        if (($this->hungriness + $hungriness) < self::MIN) {
            $this->hungriness = self::MIN;
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