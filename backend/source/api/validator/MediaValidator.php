<?php

namespace oml\api\validator;

use oml\api\controller\MediaController;
use oml\api\model\MediaModel;
use oml\api\repository\MediaRepository;
use oml\api\service\MediaService;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use PDO;
use WP_REST_Request;

class MediaValidator extends Validator
{
    private readonly MediaController $controller;
    private readonly MediaService $service;
    private readonly MediaRepository $repository;

    public function __construct()
    {
        parent::__construct(MediaModel::class);
        $this->controller = MediaController::inject();
        $this->service = MediaService::inject();
        $this->repository = MediaRepository::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $this
            ->initialize("name", $request->get_param("name"))
            ->validate("oml__required")
            ->validate("oml__name", [$this->repository])
            ->assign();

        $this
            ->initialize("description", $request->get_param("description"))
            ->validate("oml__required")
            ->validate("oml__description")
            ->assign();

        $this
            ->initialize("file", ($request->get_file_params()["file"] ?? null))
            ->validate("oml__required")
            ->validate("oml__image");

        if ($this->hasError("file") === false) {
            $this->model->path = $this->service->upload($this->parameterValue);
        }

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->create($this->model);
    }

    public function delete(WP_REST_Request $request)
    {
        $this
            ->initialize("id", $request->get_param("id"))
            ->validate("oml__required")
            ->validate("oml__id", [$this->repository])
            ->assign();

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->delete($this->model);
    }

    public function get(WP_REST_Request $request)
    {
        $this
            ->initialize("id", $request->get_param("id"))
            ->validate("oml__required")
            ->validate("oml__id", [$this->repository])
            ->assign();

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->get($this->model);
    }

    public function update(WP_REST_Request $request)
    {
        $this
            ->initialize("id", $request->get_param("id"))
            ->validate("oml__required")
            ->validate("oml__id", [$this->repository])
            ->assign();


        if ($this->hasError("id") === false) {
            $this
                ->initialize("name", $request->get_param("name"))
                ->validate("oml__required")
                ->validate("oml__name", [$this->repository, $this->model->id])
                ->assign();
        }

        $this
            ->initialize("description", $request->get_param("description"))
            ->validate("oml__required")
            ->validate("oml__description")
            ->assign();

        $this
            ->initialize("file", ($request->get_file_params()["file"] ?? null))
            ->validate("oml__image");

        if ($this->hasError("file") === false) {
            if ($this->parameterValue !== null) {
                $this->model->path = $this->service->upload($this->parameterValue);
            } else {
                $this->model->path = $this->repository->selectById($this->model->id)->path;
            }
        }

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->update($this->model);
    }

    public function list(WP_REST_Request $request)
    {
        $options = new SqlSelectOptions();

        $this
            ->initialize("pageIndex", $request->get_param("pageIndex"))
            ->validate("oml__required")
            ->validate("oml__pagination_index")
            ->assign($options);

        $this
            ->initialize("pageSize", $request->get_param("pageSize"))
            ->validate("oml__pagination_size")
            ->assign($options);

        $this
            ->initialize("search", $request->get_param("search"))
            ->validate("oml__search");

        if ($this->hasError("search") === false && $this->parameterValue !== null) {
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

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->list($options);
    }
}
