<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class ForbiddenError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::FORBIDDEN,
            "You do not have permission to perform this action",
            ["status" => 403]
        );
    }
}
