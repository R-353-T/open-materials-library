<?php

namespace oml\php\core;

use WP_REST_Response;

class LoginLimitExceededResponse extends WP_REST_Response
{
    public function __construct()
    {
        parent::__construct(["error" => "login_limit_exceeded"], 429);
    }
}
