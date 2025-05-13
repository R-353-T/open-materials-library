<?php

namespace oml\php\abstract;

abstract class Controller extends Service
{
    public static array $controllerList = [];

    /**
     * Initializes all controllers stored in the static $controllerList array
     *
     * @return void
     */
    public static function loadControllers()
    {
        foreach (self::$controllerList as $controller) {
            $instance = (object) call_user_func([$controller, "inject"]);
            self::loadControllerRoutes($instance);
        }
    }

    /**
     * Iterates over the routes declared in the $routeList array of the given
     * controller instance and registers them
     *
     * @param object $instance The controller instance
     *
     * @return void
     */
    private static function loadControllerRoutes(object $instance): void
    {
        foreach ($instance->routeList as $route) {
            [
                "endpoint" => $endpoint,
                "callback" => $callback,
                "http_method" => $http_method,
                "permission" => $permission,
                "schema" => $schema
            ] = $route;

            $args = $schema === null ? [] : $instance->schema->$schema();

            register_rest_route(
                OML_NAMESPACE,
                "/{$instance->endpoint}{$endpoint}",
                [
                    "callback"              => [$instance, $callback],
                    "methods"               => $http_method,
                    "permission_callback"   => $permission,
                    "args"                  => $args
                ]
            );
        }
    }

    protected string $endpoint;
    protected array $routeList = [];
}
