<?php

namespace oml\api\validator;

use oml\api\controller\EnumeratorController;
use oml\api\validator\EnumeratorItemValidator;
use oml\api\model\EnumeratorModel;
use oml\api\repository\EnumeratorRepository;
use oml\api\repository\QuantityRepository;
use oml\api\repository\TypeRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\error\BadRequestError;
use PDO;
use WP_REST_Request;

class EnumeratorValidator extends Validator
{
    private readonly EnumeratorController $controller;
    private readonly EnumeratorRepository $repository;
    private readonly EnumeratorItemValidator $itemValidator;

    public function __construct()
    {
        parent::__construct(EnumeratorModel::class);
        $this->controller = EnumeratorController::inject();
        $this->repository = EnumeratorRepository::inject();
        $this->itemValidator = EnumeratorItemValidator::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $this->validateName("name", $request->get_param("name"), $this->repository);
        $this->validateDescription("description", $request->get_param("description"));

        $this->initialize("typeId", $request->get_param("typeId"))
            ->validate("validator__is_required")
            ->validate("validator__database__index", [TypeRepository::inject()])
            ->validate("validator__type__is_enumerable")
            ->assign();

        $this->initialize("quantityId", $request->get_param("quantityId"))
            ->validate("validator__database__index", [QuantityRepository::inject()])
            ->assign();

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

        $this->initialize("typeId", $request->get_param("typeId"))
            ->validate("validator__is_required")
            ->validate("validator__database__index", [TypeRepository::inject()])
            ->validate("validator__type__is_enumerable")
            ->assign();

        $this->initialize("quantityId", $request->get_param("quantityId"))
            ->validate("validator__database__index", [QuantityRepository::inject()])
            ->assign();

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
            ->validate("oml__required")
            ->validate("oml__pagination_index")
            ->assign($options);

        $this->initialize("pageSize", $request->get_param("pageSize"))
            ->validate("oml__pagination_size")
            ->assign($options);

        $this->initialize("search", $request->get_param("search"))
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

    private function validateItems(string $parameter_name, mixed $parameter_value): void
    {
        $this->initialize($parameter_name, $parameter_value)
            ->validate("validator__is_required")
            ->validate("validator__is_array");

        if ($this->hasError(["id", "items", "typeId", "quantityId"]) === false) {
            $this->model->items = [];
            foreach ($this->parameterValue as $enumerator_position => $enumerator_item) {
                $this->itemValidator->item($enumerator_position, $enumerator_item, $this->model, $this->error_list);
            }
        }
    }
}
