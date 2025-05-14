<?php

namespace oml\api\schema;

use oml\api\validator\EnumeratorValidator;
use oml\php\abstract\Service;

class EnumeratorSchema extends Service
{
    private readonly EnumeratorValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->validator = EnumeratorValidator::inject();
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
            ],
            "typeId" => [
                "required" => true,
                "type" => "integer"
            ],
            "items" => [
                "required" => true,
                "type" => "array",
                "validate_callback" => [$this->validator, "validateItems"],
                "items" => [
                    "type" => "object",
                    "additionalProperties" => false,
                    "properties" => [
                        "value" => [
                            "required" => true,
                            "type" => "string"
                        ],
                        "quantityItemId" => [
                            "type" => [
                                "null",
                                "integer"
                            ]
                        ]
                    ]
                ]
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
