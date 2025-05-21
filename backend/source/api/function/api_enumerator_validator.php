<?php

use oml\api\model\EnumeratorModel;
use oml\api\repository\EnumeratorItemRepository;
use oml\php\enum\APIError;

function oml__enumerator_item_id(mixed $id, int $enumerator_id): array
{
    if ($id === null) {
        return [true, null];
    }

    $enumerator_item_repository = EnumeratorItemRepository::inject();
    $id = oml__id($id, $enumerator_item_repository);

    if ($id[0] === false) {
        return [false, $id[1]];
    }

    $enumerator_item = $enumerator_item_repository->selectById($id[1]);

    if ($enumerator_item->enumeratorId !== $enumerator_id) {
        return [false, APIError::PARAMETER_BAD_RELATION];
    }

    return [true, $id[1]];
}

function oml__enumerator_item_value(mixed $value, EnumeratorModel $enumerator): array
{
    if ($value === null) {
        return [true, null];
    }

    $value = oml__dynamic_value($value, $enumerator->typeId);

    if ($value[0] === false) {
        return [false, $value[1]];
    }

    $value = $value[1];

    foreach ($enumerator->items as $item) {
        if ($item->number === $value || $item->text === $value) {
            return [false, APIError::PARAMETER_DUPLICATE];
        }
    }

    return [true, $value];
}
