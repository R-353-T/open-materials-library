<?php

namespace oml\php\enum;

class ControllerErrorCode
{
    // Common

    public const BAD_REQUEST = "err_bad_request";
    public const FORBIDDEN = "err_forbidden";
    public const NOT_FOUND = "err_not_found";

    public const CALL_LIMIT_EXCEEDED = "err_call_limit_exceeded";
    public const LOING_LIMIT_EXCEEDED = "err_login_limit_exceeded";
}
