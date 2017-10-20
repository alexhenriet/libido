<?php

namespace Libido\FsRegistry;

use Libido\FsRegistry\RegistryException;

class Registry {

    private static $instances = array();
    private $path = null;

    /**
     * @param string $path
     * @throws RegistryException
     */
    private function __construct($path) {
        if (!is_dir($path) && false === mkdir($path, 0700, true)) {
            throw new RegistryException(sprintf('Path "%s" cannot be created.', $path));
        }
        $this->path = $path;
    }

    /**
     * @param string $path
     * @return Registry
     */
    public static function getInstance($path) {
        if (!isset($instances[$path])) {
            self::$instances[$path] = new self($path);
        }
        return self::$instances[$path];
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key) {
        return file_get_contents($this->keyPath($key));
    }

    /**
     * @param string $key
     * @param string|int $value
     */
    public function set($key, $value) {
        $this->validateKey($key);
        $this->validateValue($value);
        file_put_contents($this->keyPath($key), $value);
    }

    /**
     * @return array
     */
    public function all() {
        $a = array();
        $files = scandir($this->path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $key = str_replace('.r', '', $file);
            $a[$key] = $this->get($key);
        }
        return $a;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return file_exists($this->keyPath($key));
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function del($key) {
        $this->validateKey($key);
        if (is_file($this->keyPath($key))) {
            unlink($this->keyPath($key));
            return true;
        }
        return false;
    }

    /**
     * @param string $key
     * @throws RegistryException
     */
    private function validateKey($key) {
        $allowed = array('_', '-');
        if (!ctype_alnum(str_replace(array('_', '-'), '', $key))) {
            throw new RegistryException(sprintf('Key "%s" is invalid. Only alphanumeric chars, - and _ are allowed.', $key));
        }
    }

    /**
     * @param string $value
     * @throws RegistryException
     */
    private function validateValue($value) {
        if (!is_int($value) && !is_string($value)) {
            throw new RegistryException('Scalar value expected.');
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function keyPath($key) {
        return $this->path . DIRECTORY_SEPARATOR . strtolower($key) . '.r';
    }

}
