<?php

namespace Libido\SocketServer;

use Libido\SocketServer\SocketServer;

class TelnetChatServer extends SocketServer
{
    const KILL_PASSWORD = '123';

    public function __construct($ip = '0.0.0.0', $port = 8765)
    {
        parent::__construct($ip, $port);
    }

    protected function onClientConnect($socket)
    {
        $message = $this->id($socket) . ' has joined the chat -- ' . count($this->clients) . ' clients connected.';
        $this->log($message);
        $this->broadcast($socket, $message, true);
        $this->send($socket, 'Welcome on the chat -- ' . count($this->clients) . ' client(s) connected.');
        $this->send($socket, 'You are known has ' . $this->id($socket) . ', type /nick nickname to set your nickname.');

    }

    protected function onClientQuit($socket)
    {
        $message = $this->id($socket) . ' has left the chat -- ' . count($this->clients) . ' clients connected.';
        $this->log($message);
        $this->broadcast($socket, $message, false);
    }

    protected function onClientMessage($socket, $message)
    {
        $this->log($this->id($socket) . ' send ' . $message);
        if ($message === '/quit') {
            $this->disconnect($socket);
        } else if ($message === '/kill ' . self::KILL_PASSWORD) {
            $this->log($this->id($socket) . ' killed server.');
            $this->broadcast($socket, '--- Killed server ---');
            $this->killServer();
        } else if (preg_match('/nick (.*)/i', $message, $matches)) {
            $nick = $matches[1];
            $this->setAttribute($socket, 'nick', $nick);
            $message = $this->id($socket) . ' is now known as ' . $nick . '.';
            $this->log($message);
            $this->broadcast($socket, $message, true);
            $this->send($socket, 'You are now know as ' . $nick . '.');
        } else {
            $this->broadcast($socket, $message);
        }
    }

    protected function broadcast($emitter, $data, $skipEmitter = true)
    {
        $name = $this->id($emitter);
        if ($nick = $this->getAttribute($emitter, 'nick')) {
            $name = $nick;
        }
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = '[' . date('H:i:s') . '] <' . $name . '> ' . $line;
            parent::broadcast($emitter, $line, $skipEmitter);
        }
    }
}