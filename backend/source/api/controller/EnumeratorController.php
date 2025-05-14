<?php

namespace oml\api\controller;

use oml\api\repository\EnumeratorRepository;
use oml\php\abstract\Controller;
use oml\php\core\OkResponse;
use oml\php\core\PageResponse;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;
use oml\php\error\NotFoundError;
use PDO;
use WP_REST_Request;

class EnumeratorController extends Controller
{
    protected string $endpoint = "enumerator";
    protected array $routeList = [
        [
            "endpoint"      => "",
            "callback"      => "get",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::SUBSCRIBER,
            "schema"        => "get"
        ],
        [
            "endpoint"      => "",
            "callback"      => "delete",
            "http_method"   => ControllerHttpMethod::DELETE,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "delete"
        ],
        [
            "endpoint"      => "/create",
            "callback"      => "create",
            "http_method"   => ControllerHttpMethod::POST,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "create"
        ],
        [
            "endpoint"      => "/update",
            "callback"      => "update",
            "http_method"   => ControllerHttpMethod::POST,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "update"
        ],
        [
            "endpoint"      => "/list",
            "callback"      => "list",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::SUBSCRIBER,
            "schema"        => "list"
        ]
    ];

    private readonly EnumeratorRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = EnumeratorRepository::inject();
    }

    public function create(WP_REST_Request $request)
    {
    }

    public function delete(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $deleted = $this->repository->deleteById($id);
        return new OkResponse($deleted);
    }

    public function get(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        return new OkResponse($model);
    }

    public function list(WP_REST_Request $request)
    {
        $indexPage = $request->get_param("indexPage");
        $pageSize = $request->get_param("pageSize");
        $options = new SqlSelectOptions($indexPage, $pageSize);

        if ($request->get_param("search") !== null) {
            $options->where(
                [
                    "query" => 'LOWER(`name`) LIKE LOWER(CONCAT("%", :_search, "%"))',
                    "binds" => [
                        [":_search", $request->get_param("search"), PDO::PARAM_STR]
                    ],
                    "and" => true
                ]
            );
        }

        $options->orderBy("name", "ASC");
        $finalPage = $this->repository->finalPage($options);

        if ($finalPage < $indexPage) {
            return new NotFoundError();
        } else {
            $items = $this->repository->selectAll($options);
            return new PageResponse($items, $indexPage, $pageSize, $finalPage);
        }
    }

    public function update(WP_REST_Request $request)
    {
    }
}
