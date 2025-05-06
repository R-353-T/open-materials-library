<?php

namespace oml\php\enum;

class ControllerParamErrorCode
{
    public const REQUIRED = "required";
    public const NOT_FOUND = "not_found";
    public const ALREADY_EXISTS = "already_exists";

    public const INVALID_DATABASE_INDEX = "invalid_database_index";

    public const INVALID_MEDIA = "invalid_media";
    public const MEDIA_NOT_SUPPORTED = "media_not_supported";
    public const MEDIA_SIZE_LIMIT_EXCEEDED = "media_size_limit_exceeded";

    public const INVALID_STRING = "invalid_string";
}
