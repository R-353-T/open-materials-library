<?php

# Globals

defined("OML_VERSION") or define("OML_VERSION", "1.0.0");
defined("OML_PRODUCTION") or define("OML_PRODUCTION", wp_get_environment_type() !== "development");
defined("OML_NAMESPACE") or define("OML_NAMESPACE", "oml");

defined("OML_CONNECTION_STRING") or define(
    "OML_CONNECTION_STRING",
    "mysql:dbname=" . DB_NAME
    . ";host=" . DB_HOST
    . ";charset=" . DB_CHARSET
);

# Database

defined("OML_SQL_MIGRATION_TABLENAME") or define("OML_SQL_MIGRATION_TABLENAME", "oml_migrations");
defined("OML_SQL_UNIQUE_PARAM_PREFIX") or define("OML_SQL_UNIQUE_PARAM_PREFIX", "omlw96_");

# Directories

defined("OML_ROOT_DIR") or define("OML_ROOT_DIR", get_template_directory());
defined("OML_SQL_DIR") or define("OML_SQL_DIR", OML_ROOT_DIR . DIRECTORY_SEPARATOR . "sql");

# Dates

defined("OML_TIMEZONE") or define("OML_TIMEZONE", "Europe/Paris");
defined("OML_DATE_FORMAT") or define("OML_DATE_FORMAT", "Y-m-d H:i:s");

# Auth

defined("OML_AUTH_ENDPOINT") or define("OML_AUTH_ENDPOINT", "jwt-auth/v1");
defined("OML_AUTH_LOGIN_ENDPOINT") or define("OML_AUTH_LOGIN_ENDPOINT", "/jwt-auth/v1/token");
defined("OML_AUTH_EXPIRATION_TIME") or define("OML_AUTH_EXPIRATION_TIME", time() + DAY_IN_SECONDS * 7);

# Api

defined("OML_API_DEFAULT_PAGE_SIZE") or define("OML_API_DEFAULT_PAGE_SIZE", 32);
defined("OML_API_MAX_PAGE_SIZE") or define("OML_API_MAX_PAGE_SIZE", 128);
defined("OML_API_MAX_FILE_SIZE") or define("OML_API_MAX_FILE_SIZE", 10 * 1024 * 1024); // 10 MB

defined("OML_API_CALL_LIMIT") or define("OML_API_CALL_LIMIT", 30);
defined("OML_API_CALL_INTERVAL") or define("OML_API_CALL_INTERVAL", 6);

defined("OML_API_LOGIN_ATTEMPT_LIMIT") or define("OML_API_LOGIN_ATTEMPT_LIMIT", 5);
defined("OML_API_JAIL_TIME") or define("OML_API_JAIL_TIME", OML_PRODUCTION ? 180 : 18);

defined("OML_API_MAX_LABEL_LENGTH") or define("OML_API_MAX_LABEL_LENGTH", 255);
defined("OML_API_MAX_TEXT_LENGTH") or define("OML_API_MAX_TEXT_LENGTH", 65535);
defined("OML_API_MAX_URL_LENGTH") or define("OML_API_MAX_URL_LENGTH", 1024);

defined("OML_API_MAX_NUMBER") or define("OML_API_MAX_NUMBER", 2147483647); // PHP_MAX_INT 32 BITS
defined("OML_API_MIN_NUMBER") or define("OML_API_MIN_NUMBER", -2147483648); // PHP_MIN_INT 32 BITS

defined("OML_API_ERRCODE") or define("OML_API_ERRCODE", "code");
