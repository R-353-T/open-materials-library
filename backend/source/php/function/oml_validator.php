<?php

use oml\php\enum\APIError;

function oml_validate_database_index(mixed $value, ?object $repository = null): array
{
    $isUnsignedInt = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            "options" => ["min_range" => 1]
        ]
    );

    if ($isUnsignedInt === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    if ($repository !== null && $repository->selectById($value) === false) {
        return [false, APIError::PARAMETER_NOT_FOUND];
    }

    return [true, (int) $value];
}

function oml_validate_name(mixed $value, ?object $repository = null, ?int $id = null): array
{
    if (is_string($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    $value = trim($value);

    if (mb_strlen($value) === 0) {
        return [false, APIError::PARAMETER_STRING_EMPTY];
    }

    if (mb_strlen($value) > ___MAX_LABEL_LENGTH___) {
        return [false, APIError::PARAMATER_STRING_TOO_LONG];
    }

    $match = $repository->selectByName($value);

    if ($repository !== null && $match !== false && $match->id !== $id) {
        return [false, APIError::PARAMETER_NOT_FREE];
    }

    return [true, $value];
}

function oml_validate_description(mixed $value): array
{
    if (is_string($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    $value = trim($value);

    if (mb_strlen($value) > ___MAX_TEXT_LENGTH___) {
        return [false, APIError::PARAMATER_STRING_TOO_LONG];
    }

    return [true, $value];
}

function oml_validate_image(mixed $value, $required = true): array
{
    if (isset($value["file"]) === false) {
        if ($required) {
            return [false, APIError::PARAMETER_REQUIRED];
        } else {
            return [true, null];
        }
    }

    $value = $value["file"];

    $mime_list = [
        "png"   => "image/png",
        "jpg"   => "image/jpeg",
        "jpeg"  => "image/jpeg",
    ];

    [
        "name" => $file_name,
        "size" => $file_size
    ] = $value;

    [
        "ext"   => $file_extension,
        "type"  => $file_type
    ] = wp_check_filetype($file_name);

    if (
        isset($mime_list[$file_extension]) === false
        || $file_type !== $mime_list[$file_extension]
    ) {
        return [false, APIError::PARAMATER_IMAGE_NOT_SUPPORTED];
    }

    if ($file_size > ___MAX_IMAGE_SIZE___) {
        return [false, APIError::PARAMETER_IMAGE_TOO_LARGE];
    }

    return [true, $value];
}

function oml_validate_pagination_index(mixed $value): array
{
    if ($value === null) {
        return [false, APIError::PARAMETER_REQUIRED];
    }

    $isUnsignedInt = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            "options" => ["min_range" => 1]
        ]
    );

    if ($isUnsignedInt === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    return [true, (int) $value];
}

function oml_validate_pagination_size(mixed $value): array
{
    if ($value === null) {
        return [true, ___PAGE_SIZE___];
    }

    $isUnsignedInt = filter_var(
        $value,
        FILTER_VALIDATE_INT,
        [
            "options" => [
                "min_range" => 1,
                "max_range" => ___MAX_PAGE_SIZE___
            ]
        ]
    );

    if ($isUnsignedInt === false) {
        if ($value > ___MAX_PAGE_SIZE___) {
            return [false, APIError::PARAMATER_NUMBER_TOO_LARGE];
        } else {
            return [false, APIError::PARAMETER_INVALID];
        }
    }

    return [true, (int) $value];
}

function oml_validate_array(mixed $value): array
{
    if (is_array($value) === false) {
        return [false, APIError::PARAMETER_INVALID];
    }

    return [true, $value];
}

function oml_validate_label(mixed $value, bool $not_empty = true, bool $required = true): array
{
    if ($value === null) {
        if ($required) {
            return [false, APIError::PARAMETER_REQUIRED];
        } else {
            return [true, null];
        }
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
