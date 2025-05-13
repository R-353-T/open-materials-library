<?php

namespace oml\api\controller;

use oml\api\repository\TypeRepository;
use oml\php\abstract\Controller;
use oml\php\core\PageResponse;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;
use WP_REST_Request;

class TypeController extends Controller
{
    protected string $endpoint = "type";
    protected array $routeList = [
        [
            "endpoint"      => "/list",
            "callback"      => "list",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::SUBSCRIBER,
            "schema"        => null
        ]
    ];

    private readonly TypeRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = TypeRepository::inject();
    }

    public function list(WP_REST_Request $request)
    {
        $items = $this->repository->selectAll();
        return new PageResponse($items, 1, OML_API_MAX_PAGE_SIZE, 1);
    }
}
