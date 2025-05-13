<?php

function oml_validate_database_index(mixed $value)
{
    if ($value === null) {
        return false;
    }

    return filter_var(
        $value,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    ) !== false;
}

function oml_validate_array(mixed $value)
{
    return is_array($value);
}
