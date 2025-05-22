<?php

namespace oml\api\validator;

use oml\api\controller\QuantityController;
use oml\api\model\QuantityModel;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use PDO;
use WP_REST_Request;

class QuantityValidator extends Validator
{
    private readonly QuantityController $controller;
    private readonly QuantityRepository $repository;
    private readonly QuantityItemValidator $itemValidator;

    public function __construct()
    {
        parent::__construct(QuantityModel::class);
        $this->controller = QuantityController::inject();
        $this->repository = QuantityRepository::inject();
        $this->itemValidator = QuantityItemValidator::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $this->validateName("name", $request->get_param("name"), $this->repository);
        $this->validateDescription("description", $request->get_param("description"));
        $this->validateItems("items", $request->get_param("items"));

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->create($this->model);
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
        $this->validateDescription("description", $request->get_param("description"));
        $this->validateItems("items", $request->get_param("items"));

        if ($this->hasError("id") === false) {
            $this->validateName("name", $request->get_param("name"), $this->repository, $this->model->id);
        }

        return $this->hasError()
            ? new BadRequestError($this->error_list)
            : $this->controller->update($this->model);
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

    private function validateItems(string $parameter_name, mixed $parameter_value): void
    {
        $this->initialize($parameter_name, $parameter_value)
            ->validate("validator__is_required")
            ->validate("validator__is_array");

        if ($this->hasError(["id", "items"]) === false) {
            $this->model->items = [];
            foreach ($this->parameterValue as $quantity_position => $quantity_item) {
                $this->itemValidator->item($quantity_position, $quantity_item, $this->model, $this->error_list);
            }
        }
    }
}
