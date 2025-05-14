<?php

require_once __DIR__ . "/php/oml_constant.php";
require_once __DIR__ . "/php/function/__index__.php";

use oml\api\controller\EnumeratorController;
use oml\api\controller\MediaController;
use oml\api\controller\QuantityController;
use oml\api\controller\TestController;
use oml\api\controller\TypeController;
use oml\api\middleware\AuthLimitMiddleware;
use oml\api\middleware\BucketMiddleware;
use oml\php\abstract\Controller;
use oml\php\abstract\Middleware;
use oml\php\core\Database;

# Database

Database::initializeDatabase();

# Controllers

Controller::$controllerList = [
    EnumeratorController::class,
    TypeController::class,
    MediaController::class,
    QuantityController::class,
    TestController::class
];

# Middlewares

Middleware::$middlewareList = [
    AuthLimitMiddleware::class,
    BucketMiddleware::class,
];

# Filters

add_filter("rest_exposed_cors_headers", "oml_expose_cors_headers_filter", 10, 2);
add_filter("rest_endpoints", "oml_wp_block_original_endpoints_filter", 10, 1);
add_filter("jwt_auth_expire", "oml_jwt_expiration_time_filter", 10, 0);

add_filter("rest_pre_dispatch", [ Middleware::class, "requestFilter" ], 10, 3);
add_filter("rest_post_dispatch", [ Middleware::class, "responseFilter" ], 10, 3);

# Hooks

add_action("rest_api_init", [ Controller::class, "loadControllers" ]);
add_action("after_switch_theme", [ Database::class, "upgradeDatabase" ]);
