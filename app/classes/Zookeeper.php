<?php
class Zookeeper implements JsonSerializable
{
    private string $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
    public function feed(Animal $animal): void
    {
        echo "animal happiness ++";
        echo "animal hungriness --";
    }
    public function pet(Animal $animal): void
    {
        echo "animal happiness ++";
    }

    public function getName(): string
    {
        return $this->name;
    }
}