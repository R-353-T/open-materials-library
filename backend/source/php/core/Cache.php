<?php

namespace oml\php\core;

use oml\php\core\HashMap;

trait Cache
{
    public HashMap $cache;

    public function __construct()
    {
        $this->cache = new HashMap();
    }
}
