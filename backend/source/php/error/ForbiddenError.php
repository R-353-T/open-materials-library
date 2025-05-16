<?php

namespace oml\php\error;

use oml\api\enum\APIError;
use WP_Error;

class ForbiddenError extends WP_Error
{
    public function __construct(?array $data = null)
    {
        parent::__construct(
            APIError::FORBIDDEN,
            APIError::FORBIDDEN_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::FORBIDDEN_STATUS,
                ___API_ERROR_KEY___ => APIError::FORBIDDEN,
                ___API_DATA_KEY___ => $data
            ]
        );
    }
}
