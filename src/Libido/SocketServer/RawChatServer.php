<?php

namespace Libido\SocketServer;

use Libido\SocketServer\SocketServer;

class RawChatServer extends SocketServer
{
    public function __construct($ip = '0.0.0.0', $port = 8765)
    {
        parent::__construct($ip, $port);
    }

    protected function onClientConnect($socket)
    {
        $message = sprintf('%s has joined - %s connected.', $this->id($socket), count($this->clients));
        $this->log($message);
        $this->broadcast($socket, $message, true);
        $message = sprintf('You are connected - %s connected.', count($this->clients));
        $this->send($socket, $message);
    }

    protected function onClientQuit($socket)
    {
        $message = sprintf( '%s has left - %s connected.', $this->id($socket), count($this->clients));
        $this->log($message);
        $this->broadcast($socket, $message, false);
    }

    protected function onClientMessage($socket, $message)
    {
        $this->broadcast($socket, $message);
    }

    protected function broadcast($emitter, $data, $skipEmitter = true)
    {
        $lines = explode(PHP_EOL, $data);
        foreach ($lines as $line) {
            parent::broadcast($emitter, $line, $skipEmitter);
        }
    }
}