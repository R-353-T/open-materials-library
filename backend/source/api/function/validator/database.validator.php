<?php

use oml\php\enum\APIError;

function validator__database__index(mixed $value, object $repository): array
{
    $output = [true, $value];

    if ($value !== null) {
        $filter_var_options = ["options" => ["min_range" => 1]];
        $is_unsigned_int = filter_var($value, FILTER_VALIDATE_INT, $filter_var_options);

        if ($is_unsigned_int === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0] && $repository->selectById($value) === false) {
            $output = [false, APIError::PARAMETER_NOT_FOUND];
        }

        if ($output[0]) {
            $output[1] = (int) $value;
        }
    }

    return $output;
}

function validator__dabatase__name(mixed $value, object $repository, ?int $id = null): array
{
    $output = [true, $value];
    $strlen = 0;

    if ($value !== null) {
        $output = validator__type__label($value);

        if ($output[0]) {
            $value = $output[1];
            $strlen = mb_strlen($value);
        }

        if ($output[0] && $strlen === 0) {
            $output = [false, APIError::PARAMETER_STRING_EMPTY];
        }

        if ($output[0]) {
            $database_entity = $repository->selectByName($value);

            if ($database_entity !== false && $database_entity->id !== $id) {
                $output = [false, APIError::PARAMETER_NOT_FREE];
            }
        }
    }

    return $output;
}

function validator__database__description(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null) {
        if (is_string($value) === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0] && mb_strlen(trim($value)) > ___MAX_TEXT_LENGTH___) {
            $output = [false, APIError::PARAMATER_STRING_TOO_LONG];
        }
    }

    return $output;
}
