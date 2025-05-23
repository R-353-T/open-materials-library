<?php

# Aliases
# ----------------------------------------

defined("___DS___") or define("___DS___", DIRECTORY_SEPARATOR);

# Environment
# ----------------------------------------

defined("___VERSION___") or define("___VERSION___", "1.0");

defined("___PRODUCTION___") or define("___PRODUCTION___", wp_get_environment_type() !== "development");

defined("___NAMESPACE___") or define("___NAMESPACE___", "oml");

# Configuration
# ----------------------------------------

defined("___JAIL_TIME___") or define("___JAIL_TIME___", ___PRODUCTION___ ? 180 : 18);

# Authentication

defined("___AUTH_ENDPOINT___") or define("___AUTH_ENDPOINT___", "jwt-auth/v1");

defined("___AUTH_TOKEN_ENDPOINT___") or define("___AUTH_TOKEN_ENDPOINT___", "/" . ___AUTH_ENDPOINT___ . "/token");

defined("___AUTH_VALIDATE_ENDPOINT___") or define(
    "___AUTH_VALIDATE_ENDPOINT___",
    ___AUTH_TOKEN_ENDPOINT___ . "/validate"
);

defined("___AUTH_EXPIRATION_TIME___") or define("___AUTH_EXPIRATION_TIME___", time() + DAY_IN_SECONDS * 7);

defined("___AUTH_ATTEMPT_LIMIT___") or define("___AUTH_ATTEMPT_LIMIT___", 5);

# Date & Time

defined("___TIMEZONE___") or define("___TIMEZONE___", "Europe/Paris");

defined("___DATE_FORMAT___") or define("___DATE_FORMAT___", "Y-m-d H:i:s");

# Bucket limit

defined("___CALL_LIMIT___") or define("___CALL_LIMIT___", 30);

defined("___CALL_INTERVAL___") or define("___CALL_INTERVAL___", 6);

# Database
# ----------------------------------------

defined("___CONNECTION_STRING___") or define(
    "___CONNECTION_STRING___",
    "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . ";charset=" . DB_CHARSET
);

# Tables

defined("___DB_MIGRATION___") or define("___DB_MIGRATION___", "oml__migration");

defined("___DB_MEDIA___") or define("___DB_MEDIA___", "oml__media");

defined("___DB_QUANTITY___") or define("___DB_QUANTITY___", "oml__quantity");

defined("___DB_QUANTITY_ITEM___") or define("___DB_QUANTITY_ITEM___", "oml__quantity_item");

defined("___DB_TYPE___") or define("___DB_TYPE___", "oml__type");

defined("___DB_TYPE_INPUT___") or define("___DB_TYPE_INPUT___", "oml__type_input");

defined("___DB_ENUMERATOR___") or define("___DB_ENUMERATOR___", "oml__enumerator");

defined("___DB_ENUMERATOR_ITEM___") or define("___DB_ENUMERATOR_ITEM___", "oml__enumerator_item");

defined("___DB_CATEGORY___") or define("___DB_CATEGORY___", "oml__category");

defined("___DB_FIELD___") or define("___DB_FIELD___", "oml__field");

defined("___DB_DATASHEET___") or define("___DB_DATASHEET___", "oml__datasheet");

defined("___DB_DATASHEET_ITEM___") or define("___DB_DATASHEET_ITEM___", "oml__datasheet_item");

# Constants
# ----------------------------------------

defined("___UNIQUE_SYMBOL___") or define("___UNIQUE_SYMBOL___", "OML__av96");

defined("___API_ERROR_KEY___") or define("___API_ERROR_KEY___", "error_code");

defined("___API_STATUS_KEY___") or define("___API_STATUS_KEY___", "status");

defined("___API_DATA_KEY___") or define("___API_DATA_KEY___", "data");

defined("___MAX_ITEM_PER_RELATION___") or define("___MAX_ITEM_PER_RELATION___", 512);

# Validation
# ----------------------------------------

# String

defined("___MAX_LABEL_LENGTH___") or define("___MAX_LABEL_LENGTH___", 255);

defined("___MAX_TEXT_LENGTH___") or define("___MAX_TEXT_LENGTH___", 65535);

defined("___MAX_URL_LENGTH___") or define("___MAX_URL_LENGTH___", 2048);

# Number

defined("___MAX_NUMBER___") or define("___MAX_NUMBER___", 2147483647); // PHP_MAX_INT 32 BITS

defined("___MIN_NUMBER___") or define("___MIN_NUMBER___", -2147483648); // PHP_MIN_INT 32 BITS

# Pagination

defined("___DEFAULT_PAGE_SIZE___") or define("___DEFAULT_PAGE_SIZE___", 32);

defined("___MIN_PAGE_SIZE___") or define("___MIN_PAGE_SIZE___", 4);

defined("___MAX_PAGE_SIZE___") or define("___MAX_PAGE_SIZE___", 128);

# File

defined("___MAX_IMAGE_SIZE___") or define("___MAX_IMAGE_SIZE___", 10 * 1024 * 1024); // 10 MB

# Paths
# ----------------------------------------

defined("___ROOT_DIRECTORY___") or define("___ROOT_DIRECTORY___", get_template_directory());

defined("___SQL_DIRECTORY___") or define("___SQL_DIRECTORY___", ___ROOT_DIRECTORY___ . ___DS___ . "sql");
