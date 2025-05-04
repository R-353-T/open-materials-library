<?php

namespace oml\api\controller;

use oml\php\abstract\Controller;
use oml\php\core\OkResponse;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;
use WP_REST_Request;

class TestController extends Controller
{
    protected string $endpoint = "test";
    protected array $routeList = [
        [
            "endpoint"      => "/helloWorld",
            "callback"      => "getHelloWorld",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::ALL,
            "schema"        => null
        ]
    ];

    /**
     * @param WP_REST_Request $request
     *
     * @return OkResponse
     */
    public function getHelloWorld(WP_REST_Request $request)
    {
        return new OkResponse("Hello World!");
    }
}
