<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class NotImplementedError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::NOT_FOUND,
            "Not implemented",
            ["status" => 500]
        );
    }
}
