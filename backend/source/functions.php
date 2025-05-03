<?php

require_once __DIR__ . "/php/oml_constant.php";
require_once __DIR__ . "/php/function/__index__.php";

use oml\php\core\Database;

# Initialization

Database::initializeDatabase();

# Hooks

add_action("after_switch_theme", [ Database::class, "upgradeDatabase" ]);
