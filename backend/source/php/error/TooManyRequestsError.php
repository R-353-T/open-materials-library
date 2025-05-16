<?php

namespace oml\php\error;

use oml\php\enum\APIError;
use WP_Error;

class TooManyRequestsError extends WP_Error
{
    public function __construct(?array $data = null)
    {
        parent::__construct(
            APIError::TOO_MANY_REQUESTS,
            APIError::TOO_MANY_REQUESTS_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::TOO_MANY_REQUESTS_STATUS,
                ___API_ERROR_KEY___ => APIError::TOO_MANY_REQUESTS,
                ___API_DATA_KEY___ => $data
            ]
        );
    }
}
