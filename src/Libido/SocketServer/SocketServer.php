<?php

namespace Libido\SocketServer;

use Libido\SocketServer\SocketServerException;

class SocketServer
{
    protected $ip = null;
    protected $port = null;
    protected $up = true;
    protected $server = null;
    protected $clients = array();
    protected $attributes = array();

    /**
     * @param string $ip
     * @param int $port
     */
    public function __construct($ip = '0.0.0.0', $port = 8765)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @param resource $socket
     * @return string
     */
    protected function id($socket)
    {
        return stream_socket_get_name($socket, true);
    }

    /**
     * @param string $message
     */
    protected function log($message)
    {
        printf('[%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $message);
    }

    /**
     * @throws SocketServerException
     */
    public function run()
    {
        $connectionString = 'tcp://' . $this->ip . ':' . $this->port;
        $this->log('Starting listening on '. $connectionString);
        $this->server = stream_socket_server($connectionString, $errorNumber, $errorMessage);
        if (!$this->server) {
            throw new SocketServerException('Cannot bind socket: ' . $errorMessage);
        }
        while($this->up)
        {
            $read = $this->clients;
            $read[] = $this->server;
            if(false === stream_select($read, $write, $except, 10)) {
                throw new SocketServerException('An error occured');
            }
            if (in_array($this->server, $read)) {
                if ($clientSocket = stream_socket_accept($this->server)) {
                    $this->clients[] = $clientSocket;
                    $this->onClientConnect($clientSocket);
                }
                unset($read[array_search($this->server, $read)]);
            }
            foreach ($read as $socket) {
                $data = fread($socket, 1024);
                if (empty($data)) {
                    $this->disconnect($socket);
                    continue;
                }
                $this->onClientMessage($socket, trim($data));
            }
        }
        $this->log('Shuting down');
    }

    /**
     *
     */
    protected function killServer()
    {
        $this->up = false;
    }

    /**
     * @param resource $socket
     */
    protected function disconnect($socket)
    {
        unset($this->clients[array_search($socket, $this->clients)]);
        $this->onClientQuit($socket);
        @fclose($socket);
    }

    /**
     * @param resource $emitter
     * @param string $data
     * @param bool $skipEmitter
     */
    protected function broadcast($emitter, $data, $skipEmitter = true)
    {
        foreach ($this->clients as $socket) {
            if ($skipEmitter && ($emitter === $socket)) {
                continue;
            }
            $this->send($socket, $data);
        }
    }

    /**
     * @param resource $socket
     * @param string $data
     */
    protected function send($socket, $data)
    {
        fwrite($socket, trim($data) . PHP_EOL);
    }

    /**
     * @param resource $socket
     * @param string $name
     * @param mixed $value
     */
    protected function setAttribute($socket, $name, $value) {
        $this->attributes[$this->id($socket)][$name] = $value;
    }

    /**
     * @param resource $socket
     * @param string $name
     * @return mixed|bool
     */
    protected function getAttribute($socket, $name)
    {
        if (isset($this->attributes[$this->id($socket)][$name])) {
            return $this->attributes[$this->id($socket)][$name];
        }
        return false;
    }

    /**
     * @param resource $socket
     */
    protected function onClientConnect($socket) {}

    /**
     * @param resource $socket
     */
    protected function onClientQuit($socket) {}

    /**
     * @param resource $socket
     * @param string $message
     */
    protected function onClientMessage($socket, $message) {}
}