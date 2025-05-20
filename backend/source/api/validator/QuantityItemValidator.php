<?php

namespace oml\api\validator;

use oml\php\enum\APIError;
use oml\api\model\QuantityItemModel;
use oml\api\model\QuantityModel;
use oml\api\repository\QuantityItemRepository;
use oml\php\abstract\Validator;

class QuantityItemValidator extends Validator
{
    private readonly QuantityItemRepository $repository;

    public function __construct()
    {
        parent::__construct(QuantityItemModel::class);
        $this->repository = QuantityItemRepository::inject();
    }

    public function item(int $position, mixed $item, QuantityModel $quantity, array &$errors): array
    {
        $quantity_item = new QuantityItemModel();

        // * ------------------------------------------ *
        // * 1. Item                                    *
        // * ------------------------------------------ *

        $item = oml_validate_array($item);
        if ($item[0] === false) {
            $errors[] = [
                "parameter" => "items",
                "position" => $position,
                "error" => $item[1]
            ];
            return [false, $errors];
        }

        // * ------------------------------------------ *
        // * 2. Item id                                 *
        // * ------------------------------------------ *

        if (isset($item[1]["id"])) {
            $id = $item[1]["id"];
            $id = oml_validate_database_index($id, $this->repository);

            if ($id[0]) {
                $match = $this->repository->selectById($id[1]);

                if ($match->quantityId !== $quantity->id) {
                    $errors[] = [
                        "parameter" => "items",
                        "position" => $position,
                        "property" => "id",
                        "error" => APIError::PARAMETER_BAD_RELATION
                    ];
                    return [false, $errors];
                } else {
                    $quantity_item = $match;
                }
            } else {
                $errors[] = [
                    "parameter" => "items",
                    "position" => $position,
                    "property" => "id",
                    "error" => $id[1]
                ];
            }
        }

        // * ------------------------------------------ *
        // * 3. Item value                              *
        // * ------------------------------------------ *

        if (isset($item[1]["value"]) === false) {
            $errors[] = [
                "parameter" => "items",
                "position" => $position,
                "property" => "value",
                "error" => APIError::PARAMETER_REQUIRED
            ];
            return [false, $errors];
        }

        $value = $item[1]["value"];
        $value = oml_validate_label($value);

        // * Format *

        if ($value[0] === false) {
            $errors[] = [
                "parameter" => "items",
                "position" => $position,
                "property" => "value",
                "error" => $value[1]
            ];
            return [false, $errors];
        }

        // * Duplicate *

        $onError = false;

        foreach ($quantity->items as $qip => $qiv) {
            if ($qiv->value === $value[1]) {
                if ($onError === false) {
                    $errors[] = [
                        "parameter" => "items",
                        "position" => $position,
                        "property" => "value",
                        "error" => APIError::PARAMETER_DUPLICATE
                    ];
                }

                $errors[] = [
                    "parameter" => "items",
                    "position" => $qip,
                    "property" => "value",
                    "error" => APIError::PARAMETER_DUPLICATE
                ];

                $onError = true;
            }
        }

        if (count($errors) > 0) {
            return [false, $errors];
        }

        $quantity_item->value = $value[1];
        $quantity_item->position = $position;
        $quantity->items[] = $quantity_item;
        return [true, $quantity_item];
    }
}
