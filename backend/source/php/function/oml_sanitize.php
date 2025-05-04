<?php

function oml_sanitize_string(mixed $value): string|null
{
    if ($value === null || is_string($value) === false) {
        return null;
    }

    return trim($value);
}
