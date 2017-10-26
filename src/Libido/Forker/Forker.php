<?php

namespace Libido\Forker;

use Libido\Forker\ForkerException;

class Forker
{
    private $queue;
    private $treatment;
    private $nbForks;
    private $verbose;

    /**
     * Forker constructor.
     * @param array $queue
     * @param callable $treatment
     * @param $nbForks
     * @param bool $verbose
     * @throws ForkerException
     */
    public function __construct(array $queue, callable $treatment, $nbForks, $verbose = false)
    {
        if (!function_exists("pcntl_fork")) {
            throw new ForkerException('Forks are not supported by your system');
        }
        $this->queue = $queue;
        $this->treatment = $treatment;
        $this->nbForks = (int) $nbForks;
        $this->verbose = (bool) $verbose;
    }

    /**
     * @param array $queue
     */
    public function setQueue(array $queue)
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @param callable $treatment
     */
    public function setTreatment(callable $treatment)
    {
        $this->treatment = $treatment;
        return $this;
    }

    /**
     * @param int $nbForks
     */
    public function setNbForks($nbForks)
    {
        $this->nbForks = (int) $nbForks;
        return $this;
    }

    /**
     * @param bool $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = (bool) $verbose;
        return $this;
    }

    /**
     * main function
     */
    public function execute()
    {
        reset($this->queue);
        while (true) {
            $pids = [];
            for ($i = 1; $i <= $this->nbForks; $i++) {
                usleep(500);
                list(,$item) = each($this->queue);
                if (null === $item) {
                    break(2);
                }
                $pids[$i] = pcntl_fork();
                if (!$pids[$i]) {
                    if ($this->verbose) {
                        $this->log('Fork ' . $i . ' start');
                    }
                    $this->treatment->__invoke($item, $i);
                    if ($this->verbose) {
                        $this->log('Fork ' . $i . ' end');
                    }
                    exit();
                }
            }
            for ($i = 1; $i <= $this->nbForks; $i++) {
                if (isset($pids[$i])) {
                    pcntl_waitpid($pids[$i], $status, WUNTRACED);
                }
            }
        }
        for ($i = 1; $i <= $this->nbForks; $i++) { // Handling $this->nbForks > count($this->queue)
            if (isset($pids[$i])) {
                pcntl_waitpid($pids[$i], $status, WUNTRACED);
            }
        }
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        printf('[%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $message);
    }

}