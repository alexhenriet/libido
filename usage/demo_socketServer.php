<?php

require __DIR__ . '/../vendor/autoload.php';

use Libido\SocketServer\TelnetChatServer;

$tcs = new TelnetChatServer();
$tcs->run();