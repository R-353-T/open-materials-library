<?php

namespace oml\php\enum;

class APIError
{
    # General
    # ----------------------------------------

    public const NOT_IMPLEMENTED = "NOT_IMPLEMENTED";
    public const NOT_IMPLEMENTED_MESSAGE = "Not implemented";
    public const NOT_IMPLEMENTED_STATUS = 501;

    public const NOT_FOUND = "NOT_FOUND";
    public const NOT_FOUND_MESSAGE = "Not found";
    public const NOT_FOUND_STATUS = 404;

    public const TOO_MANY_REQUESTS = "TOO_MANY_REQUESTS";
    public const TOO_MANY_REQUESTS_MESSAGE = "Too many requests";
    public const TOO_MANY_REQUESTS_STATUS = 429;

    public const INTERNAL_SERVER_ERROR = "INTERNAL_SERVER_ERROR";
    public const INTERNAL_SERVER_ERROR_MESSAGE = "Internal server error";
    public const INTERNAL_SERVER_ERROR_STATUS = 500;

    public const FORBIDDEN = "FORBIDDEN";
    public const FORBIDDEN_MESSAGE = "Forbidden";
    public const FORBIDDEN_STATUS = 403;

    public const BAD_REQUEST = "BAD_REQUEST";
    public const BAD_REQUEST_MESSAGE = "Bad request";
    public const BAD_REQUEST_STATUS = 400;

    # Parameters
    # ----------------------------------------

    public const PARAMETER_REQUIRED = "PARAMETER_REQUIRED";

    public const PARAMETER_NOT_FOUND = "PARAMETER_NOT_FOUND";

    public const PARAMETER_NOT_FREE = "PARAMETER_NOT_FREE";

    public const PARAMETER_DUPLICATE = "PARAMETER_DUPLICATE";

    public const PARAMETER_BAD_RELATION = "PARAMETER_BAD_RELATION";

    public const PARAMETER_INVALID = "PARAMETER_INVALID";

    public const PARAMATER_IMAGE_NOT_SUPPORTED = "PARAMATER_FILE_NOT_SUPPORTED";
    public const PARAMETER_IMAGE_TOO_LARGE = "PARAMETER_FILE_TOO_LARGE";

    public const PARAMETER_STRING_EMPTY = "PARAMETER_STRING_EMPTY";
    public const PARAMATER_STRING_TOO_LONG = "PARAMATER_STRING_TOO_LONG";
    public const PARAMATER_STRING_TOO_SHORT = "PARAMATER_STRING_TOO_SHORT";

    public const PARAMATER_NUMBER_TOO_LARGE = "PARAMATER_NUMBER_TOO_LARGE";
    public const PARAMATER_NUMBER_TOO_SMALL = "PARAMATER_NUMBER_TOO_SMALL";
    public const PARAMATER_DECIMAL_EXCEEDS = "PARAMATER_DECIMAL_EXCEEDS";

    public const PARAMETER_UNAUTHORIZED = "PARAMETER_UNAUTHORIZED";
    public const PARAMETER_CIRCULAR_REFERENCE = "PARAMETER_CIRCULAR_REFERENCE";
}
