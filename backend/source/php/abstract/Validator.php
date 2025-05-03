<?php

namespace oml\php\abstract;

use oml\php\enum\ControllerParamErrorCode as ErrorCode;
use oml\php\error\BadRequestError;

abstract class Validator extends Service
{
    protected mixed $repository;
    protected BadRequestError $err;

    public function __construct(mixed $repository)
    {
        $this->repository = $repository;
        $this->err = new BadRequestError();
    }

    /**
     * Validate that the given ID is valid
     *
     * @param mixed $id The ID to validate
     * @param string $name The name of the parameter to use in the error message
     */
    public function validateId(mixed $id, string $name = 'id'): void
    {
        if ($id === null) {
            $this->err->addParameter($name, ErrorCode::REQUIRED);
            return;
        }

        if (! oml_validate_database_index($id)) {
            $this->err->addParameter($name, ErrorCode::INVALID_DATABASE_INDEX);
            return;
        }

        if ($this->repository->selectById($id) === null) {
            $this->err->addParameter($name, ErrorCode::INVALID_DATABASE_INDEX);
        }
    }
}
