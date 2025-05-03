<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class CallLimitExceededError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::CALL_LIMIT_EXCEEDED,
            "Call limit exceeded",
            ["status" => 429]
        );
    }
}
