<?php

use oml\php\enum\APIError;

function validator__file__image(mixed $value): array
{
    $output = [true, $value];

    if ($value !== null) {
        $mime_list = ["png" => "image/png", "jpg" => "image/jpeg", "jpeg" => "image/jpeg"];

        if (is_array($value) === false) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0] && (isset($value["name"]) === false || isset($value["size"]) === false)) {
            $output = [false, APIError::PARAMETER_INVALID];
        }

        if ($output[0]) {
            ["name" => $name, "size" => $size] = $value;
            ["ext" => $extension, "type" => $type] = wp_check_filetype($name);
        }

        if ($output[0] && (isset($mime_list[$extension]) === false || $type !== $mime_list[$extension])) {
            $output = [false, APIError::PARAMATER_IMAGE_NOT_SUPPORTED];
        }

        if ($output[0] && $size > ___MAX_IMAGE_SIZE___) {
            $output = [false, APIError::PARAMETER_IMAGE_TOO_LARGE];
        }
    }

    return $output;
}
