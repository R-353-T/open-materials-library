<?php

namespace oml\api\validator;

use oml\api\controller\EnumeratorController;
use oml\api\model\EnumeratorModel;
use oml\api\repository\EnumeratorRepository;
use oml\api\repository\QuantityRepository;
use oml\php\abstract\Validator;
use oml\php\core\SqlSelectOptions;
use oml\php\enum\APIError;
use oml\php\error\BadRequestError;
use PDO;
use WP_REST_Request;

class EnumeratorValidator extends Validator
{
    private readonly EnumeratorController $controller;
    private readonly EnumeratorRepository $repository;
    private readonly QuantityRepository $quantityRepository;

    public function __construct()
    {
        parent::__construct(EnumeratorModel::class);
        $this->controller = EnumeratorController::inject();
        $this->repository = EnumeratorRepository::inject();
        $this->quantityRepository = QuantityRepository::inject();
    }

    public function create(WP_REST_Request $request)
    {
        $errors = [];
        $enumerator = new EnumeratorModel();

        // * ------------------------------------------ *
        // * 1. Name                                    *
        // * ------------------------------------------ *

        $name = $request->get_param("name");
        $name = oml_validate_name($name, $this->repository);
        self::apply($name, $errors, $enumerator, "name");

        // * ------------------------------------------ *
        // * 2. Description                             *
        // * ------------------------------------------ *

        $description = $request->get_param("description");
        $description = oml_validate_description($description);
        self::apply($description, $errors, $enumerator, "description");

        // * ------------------------------------------ *
        // * 4. Type                                    *
        // * ------------------------------------------ *

        $type_id = $request->get_param("typeId");
        $type_id = oml_validate_type_is_enumerable($type_id);
        self::apply($type_id, $errors, $enumerator, "typeId");

        // * ------------------------------------------ *
        // * 5. Quantity                                *
        // * ------------------------------------------ *

        $quantity_id = $request->get_param("quantityId");
        if ($quantity_id !== null) {
            $quantity_id = oml_validate_database_index($quantity_id, $this->quantityRepository);
            self::apply($quantity_id, $errors, $enumerator, "quantityId");
        }

        // * ------------------------------------------ *
        // * 6. Items                                   *
        // * ------------------------------------------ *

        $items = $request->get_param("items");
        $items = oml_validate_array($items);
        self::apply($items, $errors, $enumerator, "items");

        if ($items[0]) {
            $enumerator->items = [];
            foreach ($items[1] as $position => $item) {
                $this->itemValidator->validate($position, $item, $enumerator, $errors);
            }
        }


        if (count($errors) === 0) {
            return $this->controller->create($enumerator);
        } else {
            return new BadRequestError($errors);
        }
    }

    public function delete(WP_REST_Request $request)
    {
        $errors = [];
        $enumerator = new EnumeratorModel();

        if (count($errors) === 0) {
            return $this->controller->delete($enumerator);
        } else {
            return new BadRequestError($errors);
        }
    }

    public function get(WP_REST_Request $request)
    {
        $errors = [];
        $enumerator = new EnumeratorModel();

        if (count($errors) === 0) {
            return $this->controller->get($enumerator);
        } else {
            return new BadRequestError($errors);
        }
    }

    public function update(WP_REST_Request $request)
    {
        $errors = [];
        $enumerator = new EnumeratorModel();

        if (count($errors) === 0) {
            return $this->controller->update($enumerator);
        } else {
            return new BadRequestError($errors);
        }
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
