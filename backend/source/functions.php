<?php

require_once __DIR__ . "/php/oml_constant.php";
require_once __DIR__ . "/php/function/__index__.php";

use oml\api\controller\TestController;
use oml\php\abstract\Controller;
use oml\php\core\Database;

# Initialization
# ----------------------------------------

Database::initializeDatabase();
Controller::$controllerList = [
    TestController::class
];

# Hooks
# ----------------------------------------

add_action("rest_api_init", [ Controller::class, "loadControllers" ]);
add_action("after_switch_theme", [ Database::class, "upgradeDatabase" ]);
