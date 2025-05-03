<?php

namespace oml\php\core;

use WP_REST_Response;

class PageResponse extends WP_REST_Response
{
    public function __construct(
        array $items,
        int $indexPage,
        int $pageSize,
        int $finalPage
    ) {
        parent::__construct(
            [
                "data" => $items,
                "indexPage" => $indexPage,
                "pageSize" => $pageSize,
                "finalPage" => $finalPage
            ],
            200
        );
    }
}
