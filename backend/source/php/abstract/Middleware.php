<?php

namespace oml\php\abstract;

use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Server;

abstract class Middleware extends Service
{
    public static $middlewareList = [];

    /**
     * This filter is used to call the "request" method of all the middlewares
     *
     * @param mixed $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return mixed The response
     */
    public static function requestFilter(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ) {
        if (oml_wp_original_request($request) === false) {
            foreach (self::$middlewareList as $middleware) {
                $instance = call_user_func(array($middleware, "inject"));
                $response = $instance->request($response, $server, $request);
            }
        }

        return $response;
    }

    /**
     * This filter is used to call the "response" method of all the middlewares (reversed)
     *
     * @param WP_HTTP_Response $response The wordpress response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return WP_HTTP_Response The response
     */
    public static function responseFilter(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ) {
        if (oml_wp_original_request($request) === false) {
            $middlewareReversedList = array_reverse(self::$middlewareList);
            foreach ($middlewareReversedList as $middleware) {
                $instance = call_user_func(array($middleware, "inject"));
                $response = $instance->response($response, $server, $request);
            }
        }

        return $response;
    }

    /**
     * Called by the "Middleware::requestFilter" filter
     *
     * @param mixed $response The response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return mixed The modified response
     */
    public function request(
        mixed $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ) {
        return $response;
    }

    /**
     * Called by the "Middleware::responseFilter" filter
     *
     * @param WP_HTTP_Response $response The response
     * @param WP_REST_Server $server The server rest api server
     * @param WP_REST_Request $request The wordpress request
     *
     * @return WP_HTTP_Response The modified response
     */
    public function response(
        WP_HTTP_Response $response,
        WP_REST_Server $server,
        WP_REST_Request $request
    ) {
        return $response;
    }

    protected string|null $userUid = null;

    public function __construct()
    {
        $this->userUid = md5($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
    }
}
