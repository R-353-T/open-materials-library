<?php

use oml\api\enum\Type;
use oml\php\enum\APIError;

function validator__type__is_enumerable(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null && in_array($value, Type::ENUMERABLE_LIST) === false) {
        $output = [false, APIError::PARAMETER_UNAUTHORIZED];
    }

    return $output;
}

function validator__type__switch(mixed $value, int $type): array
{
    switch ($type) {
        case Type::LABEL:
            return validator__type__label($value);
        case Type::NUMBER:
            return validator__type__number($value);
        case Type::MONEY:
            return validator__type__money($value);
        case Type::DURATION:
            return validator__type__duration($value);
    }

    throw new Exception("Not implemented type");
}

function validator__type__label(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null) {
        if (is_string($value) === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0]) {
            $value = trim($value);
            $strlen = mb_strlen($value);
            $output[1] = $value;
        }

        if ($output[0] && $strlen > ___MAX_LABEL_LENGTH___) {
            $output = [false, APIError::PARAMATER_STRING_TOO_LONG];
        }
    }

    return $output;
}

function validator__type__number(mixed $value): array
{
    $output = [true, $value];
    $is_int = false;

    if ($value !== null) {
        if (is_numeric($value) === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        // Integer

        if ($output[0] && filter_var($value, FILTER_VALIDATE_INT)) {
            $value = (int) $value;
            $is_int = true;
        }

        // Float

        if ($output[0] && $is_int === false && filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $value = (float) $value;

            if (mb_strlen(substr(strrchr($value, "."), 1)) > 6) {
                return [false, APIError::PARAMATER_DECIMAL_EXCEEDS];
            }
        }

        if ($output[0] && $value >= ___MAX_NUMBER___) {
            return [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
        }

        if ($output[0] && $value <= ___MIN_NUMBER___) {
            return [false, APIError::PARAMATER_NUMBER_TOO_SMALL];
        }
    }

    return $output;
}

function validator__type__money(mixed $value): array
{
    $output = [true, $value];

    if ($value === null) {
        if (is_numeric($value) === false) {
            return [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0]) {
            $value = (string) $value;
        }

        if ($output[0] && mb_strlen($value) >= ___MAX_LABEL_LENGTH___) {
            return [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
        }
    }

    return $output;
}

/**
 * @param $value milliseconds
 */
function validator__type__duration(mixed $value): array
{
    $output = [true, $value];

    if ($value === null) {
        $filter_var_options = ["options" => ["min_range" => 0]];
        $is_unsigned_int = filter_var($value, FILTER_VALIDATE_INT, $filter_var_options);

        if ($is_unsigned_int === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }
    }

    return $output;
}
