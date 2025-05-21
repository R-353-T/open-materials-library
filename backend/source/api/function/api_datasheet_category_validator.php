<?php

use oml\api\model\DatasheetCategoryModel;
use oml\api\repository\DatasheetCategoryRepository;
use oml\php\enum\APIError;

function oml__category_name(mixed $value, int $parentId, ?int $id = null): array
{

    if ($value === null) {
        return [true, null];
    }

    if (is_string($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    $value = trim($value);

    if (mb_strlen($value) === 0) {
        return [false, APIError::PARAMETER_STRING_EMPTY];
    }

    if (mb_strlen($value) > ___MAX_LABEL_LENGTH___) {
        return [false, APIError::PARAMATER_STRING_TOO_LONG];
    }

    $repository = DatasheetCategoryRepository::inject();
    $match = $repository->selectByNameAndParentId($value, $parentId);

    if ($match !== false && $match->id !== $id) {
        return [false, APIError::PARAMETER_NOT_FREE];
    }

    return [true, $value];
}

function oml__category_circular_parent_reference(mixed $value, int $id): array
{
    if ($value === $id) {
        return [false, APIError::PARAMETER_CIRCULAR_REFERENCE];
    }

    $repository = DatasheetCategoryRepository::inject();
    if ($repository->isChildOf($value, $id, true)) {
        return [false, APIError::PARAMETER_CIRCULAR_REFERENCE];
    }

    return [true, null];
}
