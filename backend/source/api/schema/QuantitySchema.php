<?php

namespace oml\api\schema;

use oml\api\enum\Type;
use oml\php\abstract\Service;

class QuantitySchema extends Service
{
    public function create()
    {
        return [
            "name" => [
                "required" => true,
                "type" => Type::LABEL
            ],
            "description" => [
                "required" => true,
                "type" => Type::TEXT
            ],
            "items" => [
                "required" => true,
                "type" => "array",
                "item" => [
                    "value" => [
                        "required" => true,
                        "type" => Type::LABEL
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
                "type" => Type::NUMBER
            ]
        ];
    }

    public function delete()
    {
        return [
            "id"    => [
                "required" => true,
                "type" => Type::NUMBER
            ]
        ];
    }

    public function list()
    {
        return [
            "search" => [
                "required" => false,
                "type" => Type::LABEL
            ],
            "indexPage" => [
                "required" => false,
                "type" => Type::NUMBER
            ],
            "pageSize" => [
                "required" => false,
                "type" => Type::NUMBER
            ]
        ];
    }

    public function update()
    {
        $schema = $this->create();

        $schema["id"] = [
            "required" => true,
            "type" => Type::NUMBER
        ];

        $schema["items"]["item"]["id"] = [
            "required" => false,
            "type" => Type::NUMBER
        ];

        return $schema;
    }
}
