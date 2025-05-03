<?php

require_once __DIR__ . "/php/oml_constant.php";
require_once __DIR__ . "/php/function/__index__.php";

use oml\api\controller\TestController;
use oml\php\abstract\Controller;
use oml\php\abstract\Middleware;
use oml\php\core\Database;

# Initialization
# ----------------------------------------

Database::initializeDatabase();

Controller::$controllerList = [TestController::class];

Middleware::$middlewareList = [];

# Filters
# ----------------------------------------

add_filter("rest_pre_dispatch", [ Middleware::class, "requestFilter" ], 10, 3);
add_filter("rest_post_dispatch", [ Middleware::class, "responseFilter" ], 10, 3);
add_filter("rest_exposed_cors_headers", [ Middleware::class, "oml_expose_cors_headers_filter" ], 10, 2);
add_filter("rest_endpoints", [ Middleware::class, "oml_wp_block_original_endpoints_filter"], 10, 1);
add_filter("jwt_auth_expire", [ Middleware::class, "oml_jwt_expiration_time_filter"], 10, 0);

# Hooks
# ----------------------------------------

add_action("rest_api_init", [ Controller::class, "loadControllers" ]);
add_action("after_switch_theme", [ Database::class, "upgradeDatabase" ]);
