<?php

namespace oml\php\core;

use WP_REST_Response;

class CallLimitExceededResponse extends WP_REST_Response
{
    public function __construct()
    {
        parent::__construct(["error" => "call_limit_exceeded"], 429);
    }
}
