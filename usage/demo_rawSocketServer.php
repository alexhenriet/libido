<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\SocketServer\RawChatServer;

$ss = new RawChatServer();
$ss->run();
