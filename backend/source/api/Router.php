<?php

namespace oml\api;

use oml\api\schema\DatasheetCategorySchema;
use oml\api\schema\EnumeratorSchema;
use oml\api\schema\MediaSchema;
use oml\api\schema\QuantitySchema;
use oml\api\schema\TypeSchema;
use oml\api\validator\DatasheetCategoryValidator;
use oml\api\validator\EnumeratorValidator;
use oml\api\validator\MediaValidator;
use oml\api\validator\QuantityValidator;
use oml\api\validator\TypeValidator;
use oml\php\abstract\Service;
use oml\php\enum\APIMethod;
use oml\php\enum\APIPermission;

class Router extends Service
{
    private function getRoutes()
    {
        return [
            // * TYPE *

            "type" => [
                "schema" => TypeSchema::inject(),
                "validator" => TypeValidator::inject(),
                "endpoints" => [
                    [
                        "endpoint"      => "/list",
                        "callback"      => "list",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER
                    ]
                ]
            ],

            // * MEDIA *

            "media" => [
                "schema" => MediaSchema::inject(),
                "validator" => MediaValidator::inject(),
                "endpoints" => [
                    [
                        "callback"      => "create",
                        "http_method"   => APIMethod::POST,
                        "permission"    => APIPermission::EDITOR
                    ],
                    [
                        "callback"      => "get",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER,
                    ],
                    [
                        "callback"      => "delete",
                        "http_method"   => APIMethod::DELETE,
                        "permission"    => APIPermission::EDITOR,
                    ],
                    [
                        "endpoint"      => "/list",
                        "callback"      => "list",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER
                    ],
                    [
                        "endpoint"      => "/update", // ! Exception (FormData required) !
                        "callback"      => "update",
                        "http_method"   => APIMethod::POST,
                        "permission"    => APIPermission::EDITOR
                    ]
                ]
            ],

            // * QUANTITY *

            "quantity" => [
                "schema" => QuantitySchema::inject(),
                "validator" => QuantityValidator::inject(),
                "endpoints" => [
                    [
                        "callback"      => "create",
                        "http_method"   => APIMethod::POST,
                        "permission"    => APIPermission::EDITOR
                    ],
                    [
                        "callback"      => "get",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER,
                    ],
                    [
                        "callback"      => "delete",
                        "http_method"   => APIMethod::DELETE,
                        "permission"    => APIPermission::EDITOR,
                    ],
                    [
                        "endpoint"      => "/list",
                        "callback"      => "list",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER
                    ],
                    [
                        "callback"      => "update",
                        "http_method"   => APIMethod::PUT,
                        "permission"    => APIPermission::EDITOR
                    ]
                ]
            ],

            // * ENUMERATOR *

            "enumerator" => [
                "schema" => EnumeratorSchema::inject(),
                "validator" => EnumeratorValidator::inject(),
                "endpoints" => [
                    [
                        "callback"      => "create",
                        "http_method"   => APIMethod::POST,
                        "permission"    => APIPermission::EDITOR
                    ],
                    [
                        "callback"      => "get",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER,
                    ],
                    [
                        "callback"      => "delete",
                        "http_method"   => APIMethod::DELETE,
                        "permission"    => APIPermission::EDITOR,
                    ],
                    [
                        "endpoint"      => "/list",
                        "callback"      => "list",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER
                    ],
                    [
                        "callback"      => "update",
                        "http_method"   => APIMethod::PUT,
                        "permission"    => APIPermission::EDITOR
                    ]
                ]
            ],

            // * DATASHEET CATEGORY *

            "category" => [
                "schema" => DatasheetCategorySchema::inject(),
                "validator" => DatasheetCategoryValidator::inject(),
                "endpoints" => [
                    [
                        "callback"      => "create",
                        "http_method"   => APIMethod::POST,
                        "permission"    => APIPermission::EDITOR
                    ],
                    [
                        "callback"      => "get",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER,
                    ],
                    [
                        "callback"      => "delete",
                        "http_method"   => APIMethod::DELETE,
                        "permission"    => APIPermission::EDITOR,
                    ],
                    [
                        "endpoint"      => "/list",
                        "callback"      => "list",
                        "http_method"   => APIMethod::GET,
                        "permission"    => APIPermission::SUBSCRIBER
                    ],
                    [
                        "callback"      => "update",
                        "http_method"   => APIMethod::PUT,
                        "permission"    => APIPermission::EDITOR
                    ]
                ]
            ]
        ];
    }

    public function loadRoutes()
    {
        $routes = $this->getRoutes();

        foreach ($routes as $routeNamespace => $routeOptions) {
            $routeEndpoints = $routeOptions["endpoints"];
            $routeValidator = $routeOptions["validator"];
            $routeSchema = $routeOptions["schema"];

            foreach ($routeEndpoints as $endpoint) {
                $endpointUrl = $endpoint["endpoint"] ?? "";

                register_rest_route(
                    ___NAMESPACE___,
                    "/{$routeNamespace}{$endpointUrl}",
                    [
                        "callback"              => [$routeValidator, $endpoint["callback"]],
                        "methods"               => $endpoint["http_method"],
                        "permission_callback"   => $endpoint["permission"]
                    ]
                );

                register_rest_route(
                    ___NAMESPACE___,
                    "/{$routeNamespace}{$endpointUrl}/schema",
                    [
                        "callback"              => [$routeSchema, $endpoint["callback"]],
                        "methods"               => $endpoint["http_method"],
                        "permission_callback"   => $endpoint["permission"]
                    ]
                );
            }
        }
    }
}
