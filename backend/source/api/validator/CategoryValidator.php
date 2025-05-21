<?php

namespace oml\api\validator;

use oml\api\controller\CategoryController;
use oml\api\model\CategoryModel;
use oml\api\repository\CategoryRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use WP_REST_Request;

class CategoryValidator extends Validator
{
    private readonly CategoryController $controller;
    private readonly CategoryRepository $repository;

    public function __construct()
    {
        parent::__construct(CategoryModel::class);
        $this->controller = CategoryController::inject();
        $this->repository = CategoryRepository::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $this
            ->initialize("parentId", $request->get_param("parentId"))
            ->validate("oml__id", [$this->repository])
            ->assign();

        if ($this->hasError("parentId") === false) {
            $this
                ->initialize("name", $request->get_param("name"))
                ->validate("oml__required")
                ->validate("oml__category_name", [$this->model->parentId])
                ->assign();
        }

        $this
            ->initialize("description", $request->get_param("description"))
            ->validate("oml__required")
            ->validate("oml__description")
            ->assign();

        $this->model->position = $this->repository->countByParentId($this->model->parentId);

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
                ->initialize("parentId", $request->get_param("parentId"))
                ->validate("oml__id", [$this->repository])
                ->validate("oml__category_circular_parent_reference", [$this->model->id])
                ->assign();

            if ($this->hasError("parentId") === false) {
                $this
                    ->initialize("name", $request->get_param("name"))
                    ->validate("oml__required")
                    ->validate("oml__category_name", [$this->model->parentId, $this->model->id])
                    ->assign();
            }
        }

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
