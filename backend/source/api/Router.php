<?php

namespace oml\api;

use oml\api\schema\MediaSchema;
use oml\api\validator\MediaValidator;
use oml\php\abstract\Service;
use oml\php\enum\APIMethod;
use oml\php\enum\APIPermission;

class Router extends Service
{
    private function getRoutes()
    {
        return [
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
                        "endpoint"      => "/update",
                        "callback"      => "update",
                        "http_method"   => APIMethod::POST,
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
                $endpointUrl = isset($endpoint["endpoint"]) ? $endpoint["endpoint"] : "";

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
