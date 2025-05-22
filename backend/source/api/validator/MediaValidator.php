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
        $this->validateName("name", $request->get_param("name"), $this->repository);
        $this->validateDescription("description", $request->get_param("description"));

        if (1) {
            $this->initialize("file", ($request->get_file_params()["file"] ?? null))
                ->validate("validator__is_required")
                ->validate("validator__file__image");

            if ($this->hasError("file") === false) {
                $this->model->path = $this->service->upload($this->parameterValue);
            }
        }

        if ($this->hasError()) {
            $response = new BadRequestError($this->error_list);
        } else {
            $response = $this->controller->create($this->model);

            if (is_wp_error($response)) {
                $this->service->delete($this->model->path);
            }
        }

        return $response;
    }

    public function delete(WP_REST_Request $request)
    {
        $this->validateId("id", $request->get_param("id"), $this->repository);
        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->delete($this->model);
    }

    public function get(WP_REST_Request $request)
    {
        $this->validateId("id", $request->get_param("id"), $this->repository);
        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->get($this->model);
    }

    public function update(WP_REST_Request $request)
    {
        $this->validateId("id", $request->get_param("id"), $this->repository);

        if ($this->hasError("id") === false) {
            $this->validateName("name", $request->get_param("name"), $this->repository, $this->model->id);
        }

        $this->validateDescription("description", $request->get_param("description"));

        if (1) {
            $this->initialize("file", ($request->get_file_params()["file"] ?? null))
                ->validate("validator__file__image");

            if ($this->hasError("file") === false) {
                if ($this->parameterValue !== null) {
                    $this->model->path = $this->service->upload($this->parameterValue);
                } else {
                    $this->model->path = $this->repository->selectById($this->model->id)->path;
                }
            }
        }

        if ($this->hasError()) {
            $response = new BadRequestError($this->error_list);
        } else {
            $response = $this->controller->update($this->model);

            if (
                is_wp_error($response)
                && $this->repository->selectById($this->model->id)->path !== $this->model->path
            ) {
                $this->service->delete($this->model->path);
            }
        }

        return $response;
    }

    public function list(WP_REST_Request $request)
    {
        $options = new SqlSelectOptions();

        $this->initialize("pageIndex", $request->get_param("pageIndex"))
            ->validate("validator__is_required")
            ->validate("validator__pagination__index")
            ->assign($options);

        $this->initialize("pageSize", $request->get_param("pageSize"))
            ->validate("validator__pagination__size")
            ->assign($options);

        $this->initialize("search", $request->get_param("search"))
            ->validate("validator__type__label");

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
