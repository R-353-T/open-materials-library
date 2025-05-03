<?php

namespace oml\php\core;

use WP_REST_Response;

class NotFoundResponse extends WP_REST_Response
{
    public function __construct($data = null)
    {
        parent::__construct(["data" => $data], 404);
    }
}
