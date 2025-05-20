<?php

namespace oml\api\validator;

use oml\api\controller\QuantityController;
use oml\api\model\QuantityModel;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\APIError;
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
        $errors = [];
        $quantity = new QuantityModel();

        // * ------------------------------------------ *
        // * 1. Name                                    *
        // * ------------------------------------------ *

        $name = $request->get_param("name");
        $name = oml_validate_name($name, $this->repository);
        self::apply($name, $errors, $quantity, "name");

        // * ------------------------------------------ *
        // * 2. Description                             *
        // * ------------------------------------------ *

        $description = $request->get_param("description");
        $description = oml_validate_description($description);
        self::apply($description, $errors, $quantity, "description");

        // * ------------------------------------------ *
        // * 3. Items                                   *
        // * ------------------------------------------ *

        $items = $request->get_param("items");
        $items = oml_validate_array($items);
        self::apply($items, $errors, $quantity, "items");

        if ($items[0]) {
            $quantity->items = [];
            foreach ($items[1] as $position => $item) {
                $this->itemValidator->validate($position, $item, $quantity, $errors);
            }
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->create($quantity);
    }

    public function delete(WP_REST_Request $request)
    {
        $errors = [];
        $quantity = new QuantityModel();

        // * ------------------------------------------ *
        // * 1. Id                                      *
        // * ------------------------------------------ *

        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        self::apply($id, $errors, $quantity, "id");

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->delete($quantity);
    }

    public function get(WP_REST_Request $request)
    {
        $errors = [];
        $quantity = new QuantityModel();

        // * ------------------------------------------ *
        // * 1. Id                                      *
        // * ------------------------------------------ *

        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        self::apply($id, $errors, $quantity, "id");

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->get($quantity);
    }

    public function update(WP_REST_Request $request)
    {
        $errors = [];
        $quantity = new QuantityModel();

        // * ------------------------------------------ *
        // * 1. Id                                      *
        // * ------------------------------------------ *

        $id = $request->get_param("id");
        $id = oml_validate_database_index($id, $this->repository);
        $hasId = self::apply($id, $errors, $quantity, "id");
        $quantity = $this->repository->selectById($quantity->id);

        // * ------------------------------------------ *
        // * 2. Name                                    *
        // * ------------------------------------------ *

        if ($hasId) {
            $name = $request->get_param("name");
            $name = oml_validate_name($name, $this->repository, $quantity->id);
            self::apply($name, $errors, $quantity, "name");
        }

        // * ------------------------------------------ *
        // * 3. Description                             *
        // * ------------------------------------------ *

        $description = $request->get_param("description");
        $description = oml_validate_description($description);
        self::apply($description, $errors, $quantity, "description");

        // * ------------------------------------------ *
        // * 4. Items                                   *
        // * ------------------------------------------ *

        $items = $request->get_param("items");
        $items = oml_validate_array($items);
        self::apply($items, $errors, $quantity, "items");

        if ($items[0]) {
            $quantity->items = [];
            foreach ($items[1] as $position => $item) {
                $this->itemValidator->validate($position, $item, $quantity, $errors);
            }
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->update($quantity);
    }

    public function list(WP_REST_Request $request)
    {
        $errors = [];
        $options = new SqlSelectOptions();

        // * ------------------------------------------ *
        // * 1. Page index                              *
        // * ------------------------------------------ *

        $index = $request->get_param("pageIndex");
        $index = oml_validate_pagination_index($index);
        self::apply($index, $errors, $options, "pageIndex");

        // * ------------------------------------------ *
        // * 2. Page size                               *
        // * ------------------------------------------ *

        $size = $request->get_param("pageSize");
        $size = oml_validate_pagination_size($size);
        self::apply($size, $errors, $options, "pageSize");

        // * ------------------------------------------ *
        // * 2. Search                                  *
        // * ------------------------------------------ *

        $search = $request->get_param("search");
        if ($search !== null) {
            if (is_string($search) === false) {
                $errors[] = [
                    "parameter" => "search",
                    APIError::PARAMETER_INVALID
                ];
            } elseif (strlen($search) > ___MAX_LABEL_LENGTH___) {
                $errors[] = [
                    "parameter" => "search",
                    APIError::PARAMATER_STRING_TOO_LONG
                ];
            } else {
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
        }

        if (count($errors) > 0) {
            return new BadRequestError($errors);
        }

        return $this->controller->list($options);
    }
}
