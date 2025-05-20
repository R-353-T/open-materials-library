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
