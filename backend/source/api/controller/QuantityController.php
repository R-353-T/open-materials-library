<?php

namespace oml\api\controller;

use oml\api\model\QuantityItemModel;
use oml\api\model\QuantityModel;
use oml\api\repository\QuantityRepository;
use oml\api\schema\QuantitySchema;
use oml\php\abstract\Controller;
use oml\php\core\OkResponse;
use oml\php\core\PageResponse;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;
use oml\php\error\NotFoundError;
use PDO;
use WP_REST_Request;

class QuantityController extends Controller
{
    protected string $endpoint = "quantity";
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

    public readonly QuantitySchema $schema;
    private readonly QuantityRepository $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = QuantityRepository::inject();
        $this->schema = QuantitySchema::inject();
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

    public function create(WP_REST_Request $request)
    {
        $model = new QuantityModel();
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");
        $items = $request->get_param("items");

        foreach ($items as $item) {
            $itemModel = new QuantityItemModel();
            $itemModel->value = $item["value"];
            $model->items[] = $itemModel;
        }

        $error = $this->repository->insert($model);

        if (is_wp_error($error)) {
            return $error;
        } else {
            return new OkResponse($model);
        }
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
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");
        $model->items = [];

        $items = $request->get_param("items");

        foreach ($items as $item) {
            $itemModel = new QuantityItemModel();

            if (isset($item["id"])) {
                $itemModel->id = $item["id"];
            }

            $itemModel->value = $item["value"];
            $model->items[] = $itemModel;
        }

        $error = $this->repository->update($model);

        if (is_wp_error($error)) {
            return $error;
        } else {
            $model = $this->repository->selectById($model->id);
            return new OkResponse($model);
        }
    }
}
