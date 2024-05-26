<?php
class Zookeeper
{
    private $name;
    public function __construct()
    {
    }
    public function feed(Animal $animal)
    {
        echo "animal happiness ++";
        echo "animal hungriness --";
    }
    public function pet(Animal $animal)
    {
        echo "animal happiness ++";
    }
}