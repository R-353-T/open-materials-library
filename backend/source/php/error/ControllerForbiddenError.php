<?php

namespace oml\php\error;

use WP_Error;

class ControllerForbiddenError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            "forbidden",
            "You do not have permission to perform this action",
            ["status" => 403]
        );
    }
}
