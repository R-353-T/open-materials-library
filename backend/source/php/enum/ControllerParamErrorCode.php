<?php

namespace oml\php\enum;

class ControllerParamErrorCode
{
    # GLOBAL

    public const REQUIRED = "rest_missing_callback_param";
    public const NOT_FOUND = "rest_not_found";
    public const ALREADY_EXISTS = "rest_already_exists";
    public const DOUBLE = "rest_double";
    public const INVALID_TYPE = "rest_invalid_type";
    public const ADDITIONAL_PROPERTIES = "rest_additional_properties_forbidden";

    # DATABASE

    public const INVALID_DATABASE_INDEX = "rest_invalid_database_index";
    public const INVALID_DATABASE_RELATION = "rest_invalid_database_relation";

    # MEDIA

    public const INVALID_MEDIA = "invalid_media";
    public const MEDIA_NOT_SUPPORTED = "media_not_supported";
    public const MEDIA_SIZE_LIMIT_EXCEEDED = "media_size_limit_exceeded";

    # STRING

    public const TOO_SHORT = "rest_too_short";
    public const TOO_LONG = "rest_too_long";
}
