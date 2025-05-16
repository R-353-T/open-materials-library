<?php

namespace oml\php\error;

use oml\api\enum\APIError;
use WP_Error;

class NotImplementedError extends WP_Error
{
    public function __construct(?array $data = null)
    {
        parent::__construct(
            APIError::NOT_IMPLEMENTED,
            APIError::NOT_IMPLEMENTED_MESSAGE,
            [
                ___API_STATUS_KEY___ => APIError::NOT_IMPLEMENTED_STATUS,
                ___API_ERROR_KEY___ => APIError::NOT_IMPLEMENTED,
                ___API_DATA_KEY___ => $data
            ]
        );
    }
}
