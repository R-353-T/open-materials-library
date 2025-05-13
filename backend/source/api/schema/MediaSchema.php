<?php

namespace oml\api\schema;

use oml\api\validator\MediaValidator;
use oml\php\abstract\Service;

class MediaSchema extends Service
{
    private readonly MediaValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->validator = MediaValidator::inject();
    }

    public function create()
    {
        return [
            "name" => [
                "required" => true,
                "type" => "string",
                "validate_callback" => [$this->validator, "validateName"]
            ],
            "description" => [
                "required" => true,
                "type" => "string",
                "maxLength" => OML_API_MAX_DESCRIPTION_LENGTH
            ]
        ];
    }

    public function delete()
    {
        return [
            "id"    => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ]
        ];
    }

    public function get()
    {
        return [
            "id"    => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ]
        ];
    }

    public function update()
    {
        return [
            "id" => [
                "required" => true,
                "type" => "integer",
                "validate_callback" => [$this->validator, "validateId"],
            ],
            "name" => [
                "required" => true,
                "type" => "string",
                "validate_callback" => [$this->validator, "validateName"]
            ],
            "description" => [
                "required" => true,
                "type" => "string",
                "maxLength" => OML_API_MAX_DESCRIPTION_LENGTH
            ]
        ];
    }

    public function list()
    {
        return [
            "search" => [
                "type" => "string",
                "maxLength" => OML_API_MAX_LABEL_LENGTH,
            ],
            "indexPage" => [
                "type" => "integer",
                "minimum" => 1,
                "default" => 1
            ],
            "pageSize" => [
                "type" => "integer",
                "maximum" => OML_API_MAX_PAGE_SIZE,
                "minimum" => OML_API_MIN_PAGE_SIZE,
                "default" => OML_API_DEFAULT_PAGE_SIZE
            ]
        ];
    }
}
