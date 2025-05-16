<?php

namespace oml\php\error;

use oml\api\enum\APIError;
use WP_Error;

class NotFoundError extends WP_Error
{
    public function __construct(?array $data = null)
    {
        parent::__construct(
            APIError::NOT_FOUND,
            APIError::NOT_FOUND_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::NOT_FOUND_STATUS,
                ___API_ERROR_KEY___ => APIError::NOT_IMPLEMENTED,
                ___API_DATA_KEY___ => $data
            ]
        );
    }
}
