<?php

namespace oml\php\abstract;

abstract class Validator extends Service
{
    public static function apply(array $result, array &$error_list, object $model, string $property): bool
    {
        if ($result[0]) {
            $model->$property = $result[1];
            return true;
        } else {
            $error_list[] = [
                "parameter" => $property,
                "error" => $result[1]
            ];
            return false;
        }
    }
}
