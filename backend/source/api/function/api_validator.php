<?php

use oml\api\enum\Type;
use oml\php\enum\APIError;

function oml_validate_type_is_enumerable(mixed $type_id)
{
    if ($type_id === null) {
        return [false, APIError::PARAMETER_REQUIRED];
    }

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
