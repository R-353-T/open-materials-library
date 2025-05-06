<?php

namespace oml\php\abstract;

use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\php\error\BadRequestError;
use WP_Error;
use WP_REST_Request;

abstract class Validator extends Service
{
    protected ?object $repository = null;

    /**
     * Validates if the given value is a valid database index
     *
     * @param mixed $value Value to be validated
     * @param WP_REST_Request $request The current HTTP request
     * @param string $name The name of the element to be validated
     *
     * @return bool|WP_Error Returns true if it is valid, otherwise returns a WP_Error
     */
    public function validateId(mixed $value, WP_REST_Request $request, string $name): bool|WP_Error
    {
        if (oml_validate_database_index($value) === false) {
            return new BadRequestError($name, ERRC::INVALID_DATABASE_INDEX);
        }

        if ($this->repository->selectById($value) === false) {
            return new BadRequestError($name, ERRC::NOT_FOUND);
        }

        return true;
    }

    /**
     * Validates if the given value is a valid name
     *
     * @param mixed $value Value to be validated
     * @param WP_REST_Request $request The current HTTP request
     * @param string $name The name of the element to be validated
     *
     * @return bool|WP_Error Returns true if it is valid, otherwise returns a WP_Error
     */
    public function validateName(mixed $value, WP_REST_Request $request, string $name): bool|WP_Error
    {
        if (oml_sanitize_string($value) === null) {
            return new BadRequestError($name, ERRC::INVALID_STRING);
        }

        $origin = $this->repository->selectByName($value);
        $id = $request->get_param("id");

        if (
            $origin !== false
            && ($id === null
            || ($id !== null && (int) $id !== $origin->id))
        ) {
            return new BadRequestError($name, ERRC::ALREADY_EXISTS);
        }

        return true;
    }
}
