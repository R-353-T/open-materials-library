<?php

require_once __DIR__ . "/php/oml_constant.php";
require_once __DIR__ . "/php/function/__index__.php";
require_once __DIR__ . "/api/function/__index__.php";

use oml\api\middleware\AuthLimitMiddleware;
use oml\api\middleware\BucketMiddleware;
use oml\api\Router;
use oml\php\abstract\Middleware;
use oml\php\core\Database;

# Middlewares

Middleware::$MIDDLEWARE_LIST = [
    AuthLimitMiddleware::class,
    BucketMiddleware::class,
];

# Filters

add_filter("jwt_auth_expire", "oml_jwt_expiration_time_filter", 10, 0);
add_filter("rest_endpoints", "oml_wp_block_original_endpoints_filter", 10, 1);
add_filter("rest_exposed_cors_headers", "oml_expose_cors_headers_filter", 10, 2);

add_filter("rest_post_dispatch", [ Middleware::class, "responseFilter" ], 10, 3);
add_filter("rest_pre_dispatch", [ Middleware::class, "requestFilter" ], 10, 3);

# Hooks

add_action("after_switch_theme", [ Database::class, "upgradeDatabase" ]);
add_action("rest_api_init", [ Router::inject(), "loadRoutes" ]);
