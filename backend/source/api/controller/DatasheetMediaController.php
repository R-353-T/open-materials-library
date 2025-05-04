<?php

namespace oml\api\controller;

use oml\api\model\DatasheetMediaModel;
use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\api\repository\DatasheetMediaRepository;
use oml\api\service\DatasheetMediaService;
use oml\api\validator\DatasheetMediaValidator;
use oml\php\abstract\Controller;
use oml\php\core\OkResponse;
use oml\php\core\PageResponse;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;
use oml\php\error\BadRequestError;
use oml\php\error\NotFoundError;
use PDO;
use WP_REST_Request;

class DatasheetMediaController extends Controller
{
    protected string $endpoint = "media";
    protected array $routeList = [
        [
            "endpoint"      => "",
            "callback"      => "get",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::SUBSCRIBER,
            "schema"        => "getSchema"
        ],
        [
            "endpoint"      => "",
            "callback"      => "delete",
            "http_method"   => ControllerHttpMethod::DELETE,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "deleteSchema"
        ],
        [
            "endpoint"      => "/create",
            "callback"      => "create",
            "http_method"   => ControllerHttpMethod::POST,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "createSchema"
        ],
        [
            "endpoint"      => "/update",
            "callback"      => "update",
            "http_method"   => ControllerHttpMethod::POST,
            "permission"    => ControllerPermission::EDITOR,
            "schema"        => "updateSchema"
        ],
        [
            "endpoint"      => "/list",
            "callback"      => "list",
            "http_method"   => ControllerHttpMethod::GET,
            "permission"    => ControllerPermission::SUBSCRIBER,
            "schema"        => "listSchema"
        ]
    ];

    private readonly DatasheetMediaRepository $repository;
    private readonly DatasheetMediaValidator $validator;
    private readonly DatasheetMediaService $service;

    public function __construct()
    {
        parent::__construct();
        $this->repository = DatasheetMediaRepository::inject();
        $this->validator = DatasheetMediaValidator::inject();
        $this->service = DatasheetMediaService::inject();
    }

    # ----------------------------------------
    # GET
    # ----------------------------------------

    /**
     * Validation schema for the get endpoint
     *
     * @return array
     */
    public function getSchema()
    {
        return [
            "id"    => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ]
        ];
    }

    /**
     * Returns a datasheet media by id
     *
     * @param WP_REST_Request $request The request
     *
     * @return OkResponse The response
     */
    public function get(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        return new OkResponse($model);
    }

    # ----------------------------------------
    # DELETE
    # ----------------------------------------

    /**
     * Validation schema for the delete endpoint
     *
     * @return array
     */
    public function deleteSchema()
    {
        return [
            "id"    => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ]
        ];
    }

    /**
     * Deletes a datasheet media by id
     *
     * @param WP_REST_Request $request The request
     *
     * @return OkResponse The response
     */
    public function delete(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $deleted = $this->repository->deleteById($id);
        return new OkResponse($deleted);
    }

    # ----------------------------------------
    # CREATE
    # ----------------------------------------

    /**
     * Validation schema for the create endpoint
     *
     * @return array
     */
    public function createSchema()
    {
        return [
            "name" => [
                "required" => true,
                "type" => "string",
                "minLength" => OML_API_MIN_NAME_LENGTH,
                "maxLength" => OML_API_MAX_LABEL_LENGTH,
                "validate_callback" => [$this->validator, "validateName"]
            ],
            "description" => [
                "required" => true,
                "type" => "string",
                "maxLength" => OML_API_MAX_DESCRIPTION_LENGTH
            ]
        ];
    }

    /**
     * Creates a new datasheet media
     *
     * @param WP_REST_Request $request The request
     *
     * @return OkResponse The response
     */
    public function create(WP_REST_Request $request)
    {
        $model = new DatasheetMediaModel();
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");

        # START VALIDATE FILE

        if (isset($request->get_file_params()["file"]) === false) {
            return new BadRequestError("file", ERRC::REQUIRED);
        }

        $file = $request->get_file_params()["file"];
        $err = $this->validator->validateFile($file, $request, "file");
        if ($err !== true) {
            return $err;
        }

        # END VALIDATE FILE

        $model->path = $this->service->upload($file);
        $error = $this->repository->insert($model);

        if (is_wp_error($error)) {
            $this->service->delete($model->path);
            return $error;
        } else {
            return new OkResponse($model);
        }
    }

    # ----------------------------------------
    # UPDATE
    # ----------------------------------------

    /**
     * Validation schema for the update endpoint
     *
     * @return array
     */
    public function updateSchema()
    {
        return [
            "id" => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ],
            "name" => [
                "required" => true,
                "type" => "string",
                "minLength" => OML_API_MIN_NAME_LENGTH,
                "maxLength" => OML_API_MAX_LABEL_LENGTH,
                "validate_callback" => [$this->validator, "validateName"]
            ],
            "description" => [
                "required" => true,
                "type" => "string",
                "maxLength" => OML_API_MAX_DESCRIPTION_LENGTH
            ]
        ];
    }

    /**
     * Updates a datasheet media by id
     *
     * @param WP_REST_Request $request The request
     *
     * @return OkResponse The response
     */
    public function update(WP_REST_Request $request)
    {
        $id = $request->get_param("id");
        $model = $this->repository->selectById($id);
        $model->name = $request->get_param("name");
        $model->description = $request->get_param("description");
        $oldFilePath = $model->path;

        # START VALIDATE FILE

        if (isset($request->get_file_params()["file"])) {
            $file = $request->get_file_params()["file"];
            $err = $this->validator->validateFile($file, $request, "file");
            if ($err !== true) {
                return $err;
            }
            $model->path = $this->service->upload($file);
        }

        # END VALIDATE FILE

        $error = $this->repository->update($model);

        if (is_wp_error($error)) {
            $this->service->delete($model->path);
            return $error;
        } else {
            if ($model->path !== $oldFilePath) {
                $this->service->delete($oldFilePath);
            }

            return new OkResponse($model);
        }
    }

    # ----------------------------------------
    # LIST
    # ----------------------------------------

    /**
     * Validation schema for the list endpoint
     *
     * @return array
     */
    public function listSchema()
    {
        return [
            "search" => [
                "type" => "string",
                "maxLength" => OML_API_MAX_LABEL_LENGTH,
            ],
            "indexPage" => [
                "type" => "integer",
                "minimum" => 1,
                "default" => 1
            ],
            "pageSize" => [
                "type" => "integer",
                "maximum" => OML_API_MAX_PAGE_SIZE,
                "minimum" => OML_API_MIN_PAGE_SIZE,
                "default" => OML_API_DEFAULT_PAGE_SIZE
            ]
        ];
    }

    /**
     * Lists all datasheet media
     *
     * @param WP_REST_Request $request The request
     *
     * @return PageResponse The response
     */
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
}
