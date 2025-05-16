<?php

namespace oml\php\abstract;

abstract class Validator extends Service
{
    public static function apply(array $result, array &$errors, object $model, string $property): void
    {
        if ($result[0]) {
            $model->$property = $result[1];
        } else {
            $errors[$property] = $result[1];
        }
    }
}
