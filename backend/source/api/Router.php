<?php

namespace oml\api;

use oml\api\schema\MediaSchema;
use oml\api\validator\MediaValidator;
use oml\php\abstract\Service;
use oml\php\enum\ControllerHttpMethod;
use oml\php\enum\ControllerPermission;

class Router extends Service
{
    private function getRoutes()
    {
        return [
            // * MEDIA *

            "media" => [
                [
                    "schema" => MediaSchema::inject(),
                    "validator" => MediaValidator::inject(),
                    "endpoints" => [
                        [
                            "callback"      => "create",
                            "http_method"   => ControllerHttpMethod::POST,
                            "permission"    => ControllerPermission::EDITOR
                        ],
                        [
                            "callback"      => "get",
                            "http_method"   => ControllerHttpMethod::GET,
                            "permission"    => ControllerPermission::SUBSCRIBER,
                        ],
                        [
                            "callback"      => "delete",
                            "http_method"   => ControllerHttpMethod::DELETE,
                            "permission"    => ControllerPermission::EDITOR,
                        ],
                        [
                            "endpoint"      => "/list",
                            "callback"      => "list",
                            "http_method"   => ControllerHttpMethod::GET,
                            "permission"    => ControllerPermission::SUBSCRIBER
                        ],
                        [
                            "endpoint"      => "/update",
                            "callback"      => "update",
                            "http_method"   => ControllerHttpMethod::POST,
                            "permission"    => ControllerPermission::EDITOR
                        ]
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
                    OML_NAMESPACE,
                    "/{$routeNamespace}{$endpointUrl}",
                    [
                        "callback"              => [$routeValidator, $endpoint["callback"]],
                        "methods"               => $endpoint["http_method"],
                        "permission_callback"   => $endpoint["permission"]
                    ]
                );

                register_rest_route(
                    OML_NAMESPACE,
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
