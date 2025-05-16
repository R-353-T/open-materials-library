<?php

namespace oml\php\error;

use oml\php\enum\APIError;
use WP_Error;

class BadRequestError extends WP_Error
{
    public function __construct(?array $data = null)
    {
        parent::__construct(
            APIError::BAD_REQUEST,
            APIError::BAD_REQUEST_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::BAD_REQUEST_STATUS,
                ___API_ERROR_KEY___ => APIError::BAD_REQUEST,
                ___API_DATA_KEY___ => $data
            ]
        );
    }
}
