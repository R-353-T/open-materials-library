<?php

namespace oml\api\schema;

use oml\api\enum\Type;
use oml\php\abstract\Service;

class DatasheetCategorySchema extends Service
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
            "parentId" => [
                "required" => false,
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

    public function get()
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
            "parentId" => [
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

        return $schema;
    }
}
