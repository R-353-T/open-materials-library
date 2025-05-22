<?php

use oml\api\model\QuantityModel;
use oml\api\repository\QuantityItemRepository;
use oml\php\enum\APIError;

function validator__quantity__item_id(mixed $value, int $quantity_id): array
{
    $quantity_item_repository = QuantityItemRepository::inject();
    $output = [true, null];

    if ($value !== null) {
        $output = validator__database__index($value, $quantity_item_repository);

        if ($output[0]) {
            $value = $output[1];
            $quantity_item = $quantity_item_repository->selectById($value);

            if ($quantity_item->quantityId !== $quantity_id) {
                return [false, APIError::PARAMETER_BAD_RELATION];
            }
        }
    }

    return $output;
}

function validator__quantity__item_value(mixed $value, QuantityModel $quantity): array
{
    $output = [true, null];

    if ($value !== null) {
        $output = validator__type__label($value);

        if ($output[0]) {
            $lower_value = strtolower($output[1]);

            foreach ($quantity->items as $item) {
                if (strtolower($item->value) === $lower_value) {
                    return [false, APIError::PARAMETER_DUPLICATE];
                }
            }
        }
    }

    return $output;
}
