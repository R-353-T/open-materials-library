<?php

use oml\php\enum\APIError;

function validator__is_required(mixed $value): array
{
    $output = [true, $value];

    if ($value === null) {
        $output = [false, APIError::PARAMETER_REQUIRED];
    }

    return $output;
}

function validator__is_array(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null && is_array($value) === false) {
        $output = [false, APIError::PARAMETER_INVALID];
    }

    return $output;
}
