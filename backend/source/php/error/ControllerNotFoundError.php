<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class ControllerNotFoundError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::NOT_FOUND,
            "Not found",
            ["status" => 404]
        );
    }
}
