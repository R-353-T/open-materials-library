<?php

namespace oml\php\function;

use oml\php\abstract\Repository;

function oml_validate_database_index(mixed $value, Repository $repository)
{
    $isUnsignedInt = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            "options" => ["min_range" => 1]
        ]
    );

    return $isUnsignedInt !== false
        && $repository->selectById($value) === false;
}

function oml_validate_empty_string(mixed $value): bool
{
    return $value !== null
    && $value === "";
}
