<?php
namespace App;
use JsonSerializable;
class ZooKeeper implements JsonSerializable
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
    public function getName(): string
    {
        return $this->name;
    }
}