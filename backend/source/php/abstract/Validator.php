<?php

namespace oml\php\abstract;

use oml\php\enum\ControllerParamErrorCode as ERRC;
use oml\php\error\BadRequestError;
use WP_Error;
use WP_REST_Request;

abstract class Validator extends Service
{
    protected ?object $repository = null;

    public function validateId(mixed $value, WP_REST_Request $request, string|array $name): bool|WP_Error
    {
        if (oml_validate_database_index($value) === false) {
            return new BadRequestError($name, ERRC::INVALID_DATABASE_INDEX);
        }

        if ($this->repository->selectById($value) === false) {
            return new BadRequestError($name, ERRC::NOT_FOUND);
        }

        return true;
    }

    public function validateName(mixed $value, WP_REST_Request $request, string $name): bool|WP_Error
    {
        if (oml_sanitize_string($value) === null) {
            return new BadRequestError($name, ERRC::INVALID_TYPE);
        }

        if (strlen(oml_sanitize_string($value)) < OML_API_MIN_NAME_LENGTH) {
            return new BadRequestError($name, ERRC::TOO_SHORT);
        }

        if (strlen($value) > OML_API_MAX_LABEL_LENGTH) {
            return new BadRequestError($name, ERRC::TOO_LONG);
        }

        if (isset($this->repository)) {
            $origin = $this->repository->selectByName($value);
            $id = $request->get_param("id");

            if (
                $origin !== false
                && ($id === null
                || ($id !== null && (int) $id !== $origin->id))
            ) {
                return new BadRequestError($name, ERRC::ALREADY_EXISTS);
            }
        }

        return true;
    }
}
