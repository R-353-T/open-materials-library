<?php

namespace oml\php\abstract;

use WP_REST_Response;

abstract class Controller extends Service
{
    protected function OK(mixed $data = null)
    {
        return new WP_REST_Response(["data" => $data]);
    }

    protected function OKPage(array $data, int $index, int $size, int $final)
    {
        return new WP_REST_Response(
            [
                "data" => $data,
                "page" => [
                    "index" => $index,
                    "size" => $size,
                    "final" => $final
                ]
            ]
        );
    }
}
