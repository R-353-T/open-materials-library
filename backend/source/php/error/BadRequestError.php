<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class BadRequestError extends WP_Error
{
    public function __construct(string $property, string $errorCode)
    {
        parent::__construct(
            ControllerErrorCode::BAD_REQUEST,
            $errorCode,
            [
                "status" => 400,
                "property" => $property,
                OML_API_ERRCODE => $errorCode
            ]
        );
    }
}
