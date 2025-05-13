<?php

namespace oml\php\abstract;

use oml\php\abstract\Singleton;

abstract class Service
{
    use Singleton;

    public function __construct()
    {
    }
}
