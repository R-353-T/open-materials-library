<?php

namespace oml\php\core;

class HashMap
{
    private array $map = [];

    public function set(string $key, mixed $value)
    {
        $this->map[$key] = $value;
    }

    public function contains(string $key)
    {
        return isset($this->map[$key]);
    }

    public function get(string $key)
    {
        return $this->map[$key] ?? false;
    }

    public function remove(string $key)
    {
        unset($this->map[$key]);
    }

    public function clear()
    {
        $this->map = [];
    }
}
