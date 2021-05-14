<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\Console\DynamicMultiChoice;


$choices = ['PHP', 'Ruby', 'Python', 'Perl', 'Nodejs'];
$m = new DynamicMultiChoice($choices);
$choice = $m->ask('What is your favorite language ?');
printf("You selected: %s\n", DynamicMultiChoice::bold($choice));