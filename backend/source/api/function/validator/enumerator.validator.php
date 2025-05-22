<?php

use oml\api\model\EnumeratorModel;
use oml\api\repository\EnumeratorItemRepository;
use oml\php\enum\APIError;

function validator__enumerator__item_id(mixed $value, int $enumerator_id): array
{
    $enumerator_item_repository = EnumeratorItemRepository::inject();
    $output = [true, null];

    if ($value !== null) {
        $output = validator__database__index($value, $enumerator_item_repository);

        if ($output[0]) {
            $value = $output[1];
            $enumerator_item = $enumerator_item_repository->selectById($value);

            if ($enumerator_item->enumeratorId !== $enumerator_id) {
                return [false, APIError::PARAMETER_BAD_RELATION];
            }
        }
    }

    return $output;
}

function validator__enumerator__item_value(mixed $value, EnumeratorModel $enumerator): array
{
    $output = [true, null];

    if ($value !== null) {
        $output = validator__type__switch($value, $enumerator->typeId);

        if ($output[0]) {
            if (is_string($value)) {
                $value = strtolower($output[1]);
            }

            foreach ($enumerator->items as $item) {
                if (strtolower($item->value) === $value) {
                    return [false, APIError::PARAMETER_DUPLICATE];
                }
            }
        }
    }

    return $output;
}
