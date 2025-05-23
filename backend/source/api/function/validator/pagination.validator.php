<?php

use oml\php\enum\APIError;

function validator__pagination__index(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null) {
        $filter_var_options = ["options" => ["min_range" => 1]];
        $is_unsigned_int = filter_var($value, FILTER_VALIDATE_INT, $filter_var_options);

        if ($is_unsigned_int === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0]) {
            $output[1] = (int) $value;
        }
    }

    return $output;
}

function validator__pagination__size(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null) {
        $filter_var_options = ["options" => ["min_range" => 1]];
        $is_unsigned_int = filter_var($value, FILTER_VALIDATE_INT, $filter_var_options);

        if ($is_unsigned_int === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0] && $value > ___MAX_PAGE_SIZE___) {
            $output = [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
        }

        if ($output[0]) {
            $output[1] = (int) $value;
        }
    } else {
        $output[1] = ___DEFAULT_PAGE_SIZE___;
    }

    return $output;
}
