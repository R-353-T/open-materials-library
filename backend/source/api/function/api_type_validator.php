<?php

use oml\api\enum\Type;
use oml\api\repository\TypeRepository;
use oml\php\enum\APIError;

function oml__type_is_enumerable(mixed $type_id): array
{
    if ($type_id === null) {
        return [true, null];
    }

    $id = oml__id($type_id, TypeRepository::inject());

    if ($id[0] === false) {
        return [false, $id[1]];
    }

    $type_id = $id[1];

    if (
        in_array($type_id, [
        Type::LABEL,
        Type::NUMBER,
        Type::MONEY,
        Type::DURATION
        ]) === false
    ) {
        return [false, APIError::PARAMETER_UNAUTHORIZED];
    }

    return [true, $type_id];
}

function oml__dynamic_value(mixed $value, int $typeId): array
{
    switch ($typeId) {
        case Type::LABEL:
            return oml__label($value);
        case Type::NUMBER:
            return oml__number($value);
        case Type::MONEY:
            return oml__money($value);
        case Type::DURATION:
            return oml__duration($value);
    }

    throw new Exception("Not implemented type");
}

function oml__label(mixed $value, bool $not_empty = true): array
{
    if ($value === null) {
        return [true, null];
    }

    if (is_string($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    $value = trim($value);

    if (mb_strlen($value) > ___MAX_LABEL_LENGTH___) {
        return [false, APIError::PARAMATER_STRING_TOO_LONG];
    }

    if (mb_strlen($value) === 0 && $not_empty) {
        return [false, APIError::PARAMETER_STRING_EMPTY];
    }

    return [true, $value];
}

function oml__number(mixed $value): array
{
    if ($value === null) {
        return [true, null];
    }

    if (is_numeric($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    if ((int) $value >= ___MAX_NUMBER___) {
        return [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
    }

    if ((int) $value <= ___MIN_NUMBER___) {
        return [false, APIError::PARAMATER_NUMBER_TOO_SMALL];
    }

    $value = (float) $value;

    if (mb_strlen(substr(strrchr($value, "."), 1)) > 6) {
        return [false, APIError::PARAMATER_DECIMAL_EXCEEDS];
    }

    return [true, $value];
}

function oml__money(mixed $value): array
{
    if ($value === null) {
        return [true, null];
    }

    if (is_numeric($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    $value = (string) $value;

    if (mb_strlen($value) >= ___MAX_LABEL_LENGTH___) {
        return [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
    }

    return [true, $value];
}

function oml__duration(mixed $value): array
{
    if ($value === null) {
        return [true, null];
    }

    throw new Exception("Not implemented");
}
