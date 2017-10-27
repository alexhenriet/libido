<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\Forker\Forker;

$ips = ipRange('192.168.0.0', '192.168.255.255');

$treatment = function($ip, $forkId) {
    printf('Fork %d treating IP %s' . PHP_EOL, $forkId, $ip);
    if(@fsockopen($ip, 80, $errno, $errstr, 1)) {
        touch('/tmp/open/' . $ip);
    }
};

$f = new Forker($ips, $treatment, 20, false);
$f->execute();

function ipRange($start, $end) {
    $start = ip2long($start);
    $end = ip2long($end);
    return array_map('long2ip', range($start, $end) );
}