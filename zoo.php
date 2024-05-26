<?php

//Dependencies

require_once 'app/classes/Animal.php';
require_once 'app/classes/Zookeeper.php';
require_once 'app/classes/Zoo.php';
require 'vendor/autoload.php';
use Carbon\Carbon;
use Symfony\Component\Console\Helper\Table;

$table = new Table($output);
$table
    ->setHeaders(['ISBN', 'Title', 'Author'])
    ->setRows([
        ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
        ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
        ['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
        ['80-902734-1-6', 'And Then There Were None', 'Agatha Christie'],
    ])
;
$table->render();





