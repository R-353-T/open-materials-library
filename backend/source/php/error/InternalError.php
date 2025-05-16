<?php

namespace oml\php\error;

use oml\php\enum\APIError;
use WP_Error;

class InternalError extends WP_Error
{
    public function __construct(string $message, string $trace, array $data = [])
    {
        parent::__construct(
            APIError::INTERNAL_SERVER_ERROR,
            APIError::INTERNAL_SERVER_ERROR_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::INTERNAL_SERVER_ERROR_STATUS,
                ___API_ERROR_KEY___ => APIError::INTERNAL_SERVER_ERROR,
                ___API_DATA_KEY___ => [
                    "message" => $message,
                    "trace" => $trace,
                    ...$data
                ]
            ]
        );
    }
}
