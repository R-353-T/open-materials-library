<?php

namespace oml\api\validator;

use oml\api\controller\DatasheetCategoryController;
use oml\api\model\DatasheetCategoryModel;
use oml\api\repository\DatasheetCategoryRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use WP_REST_Request;

class DatasheetCategoryValidator extends Validator
{
    private readonly DatasheetCategoryController $controller;
    private readonly DatasheetCategoryRepository $repository;

    public function __construct()
    {
        parent::__construct(DatasheetCategoryModel::class);
        $this->controller = DatasheetCategoryController::inject();
        $this->repository = DatasheetCategoryRepository::inject();
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
            ->initialize("name", $request->get_param("name"))
            ->validate("oml__required")
            ->validate("oml__name", [$this->repository])
            ->assign();

        $this
            ->initialize("description", $request->get_param("description"))
            ->validate("oml__required")
            ->validate("oml__description")
            ->assign();

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->update($this->model);
    }

    public function list(WP_REST_Request $request)
    {
        $options = new SqlSelectOptions();

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->list($options);
    }
}
