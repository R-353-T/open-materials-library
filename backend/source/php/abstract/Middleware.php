<?php

namespace oml\php\abstract;

use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Server;

abstract class Middleware extends Service
{
    public static $middlewareList = [];

    public static function requestFilter(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): mixed {
        if (oml_wp_original_request($request) === false) {
            foreach (self::$middlewareList as $middleware) {
                $instance = call_user_func(array($middleware, "inject"));
                $response = $instance->request($response, $server, $request);
            }
        }

        return $response;
    }

    public static function responseFilter(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): WP_HTTP_Response {
        if (oml_wp_original_request($request) === false) {
            $middlewareReversedList = array_reverse(self::$middlewareList);
            foreach ($middlewareReversedList as $middleware) {
                $instance = call_user_func(array($middleware, "inject"));
                $response = $instance->response($response, $server, $request);
            }
        }

        return $response;
    }

    protected readonly string $userUid;

    public function __construct()
    {
        $this->userUid = md5($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
    }

    public function request(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): mixed {
        return $response;
    }

    public function response(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ): WP_HTTP_Response {
        return $response;
    }
}
