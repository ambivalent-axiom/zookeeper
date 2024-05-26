<?php
class Animal
{
    protected string $name;
    protected array $bestFood;
    protected $happiness;
    protected $hungriness;
    public function __construct(
        string $name,
        array $bestFood
        )
    {
        $this->name = $name;
        $this->bestFood = $bestFood;
        $this->hungriness = 100;
        $this->happiness = 100;
    }

    protected function play() {
        echo "happy ++";
        echo "food --";
    }
    protected function work() {
        echo "happy --";
        echo "food --";
    }
    protected function validateFood() {
        if(in_array('food', $this->bestFood)) {
            echo "food++ and happy ++";
        } else {
            echo "food---- and happy --";
        }
    }
}

class Fish extends Animal
{
    public function __construct(string $name, array $bestFood)
    {
        parent::__construct($name, $bestFood);
    }
}
class Mammal extends Animal
{
    public function __construct(string $name, array $bestFood)
    {
        parent::__construct($name, $bestFood);
    }
}
class Amphibian extends Animal
{
    public function __construct(string $name, array $bestFood)
    {
        parent::__construct($name, $bestFood);
    }
}
class Bird extends Animal
{
    public function __construct(string $name, array $bestFood)
    {
        parent::__construct($name, $bestFood);
    }
}
class Reptile extends Animal
{
    public function __construct(string $name, array $bestFood)
    {
        parent::__construct($name, $bestFood);
    }
}