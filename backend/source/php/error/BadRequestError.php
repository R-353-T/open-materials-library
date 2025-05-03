<?php

namespace oml\php\error;

use oml\php\enum\ControllerErrorCode;
use WP_Error;

class BadRequestError extends WP_Error
{
    public function __construct()
    {
        parent::__construct(
            ControllerErrorCode::BAD_REQUEST,
            "Bad request",
            [
                "status" => 400,
                "parameters" => []
            ],
        );
    }

    public function addParameter(string $index, string $errorCode, array $parameters = [])
    {
        $error = [
            "index" => $index,
            "errorCode" => $errorCode,
            "parameters" => $parameters
        ];

        $this->error_data[400]["parameters"][] = $error;

        return $error;
    }

    /**
     * Returns true if the error list contains any parameter errors
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->error_data[400]["parameters"]) === 0;
    }
}
