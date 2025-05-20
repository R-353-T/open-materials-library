<?php

use oml\api\model\QuantityModel;
use oml\api\repository\QuantityItemRepository;
use oml\php\enum\APIError;

function oml__quantity_item_id(mixed $id, int $quantity_id): array
{
    if ($id === null) {
        return [true, null];
    }

    $quantity_item_repository = QuantityItemRepository::inject();

    $id = oml__id($id, $quantity_item_repository);

    if ($id[0] === false) {
        return [false, $id[1]];
    }

    $quantity_item = $quantity_item_repository->selectById($id[1]);

    if ($quantity_item->quantityId !== $quantity_id) {
        return [false, APIError::PARAMETER_BAD_RELATION];
    }

    return [true, $id[1]];
}

function oml__quantity_item_value(mixed $value, QuantityModel $quantity): array
{
    if ($value === null) {
        return [true, null];
    }

    $lower_value = strtolower($value);

    foreach ($quantity->items as $item) {
        if (strtolower($item->value) === $lower_value) {
            return [false, APIError::PARAMETER_DUPLICATE];
        }
    }

    return [true, $value];
}
