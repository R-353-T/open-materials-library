<?php

namespace oml\php\error;

use WP_Error;

class InternalError extends WP_Error
{
    public function __construct(string $message, string $trace)
    {
        parent::__construct(500, $message, $trace);
    }
}
