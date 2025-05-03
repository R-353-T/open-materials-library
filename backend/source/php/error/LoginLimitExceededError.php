<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class LoginLimitExceededError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::LOING_LIMIT_EXCEEDED,
            "Login limit exceeded",
            ["status" => 429]
        );
    }
}
