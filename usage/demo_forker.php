<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\Forker\Forker;

$queue = array('cat', 'dog', 'mermaid', 'unicorn');

$treatment = function($item, $forkNumber) {
    $duration = rand(1,10);
    printf('Fork %d - Sleeping %s seconds' . PHP_EOL, $forkNumber, $duration);
    sleep($duration);
    printf('Fork %d - Treating item %s' . PHP_EOL, $forkNumber, $item);
};

printf('===== Verbose execution with 20 forks =====' . PHP_EOL);
$f = new Forker($queue, $treatment, 20, true);
$f->execute();
printf(PHP_EOL);
printf('===== Non-verbose execution with 2 forks =====' . PHP_EOL);
$f->setNbForks(2)->setVerbose(false)->execute();