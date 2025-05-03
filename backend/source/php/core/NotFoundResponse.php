<?php

namespace oml\php\core;

use WP_REST_Response;

class NotFoundResponse extends WP_REST_Response
{
    public function __construct()
    {
        parent::__construct(["error" => "not_found"], 404);
    }
}
