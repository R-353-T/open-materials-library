<?php

namespace oml\php\enum;

use WP_REST_Server;

class ControllerHttpMethod
{
    public const POST = WP_REST_Server::CREATABLE;
    public const GET = WP_REST_Server::READABLE;
    public const PUT = WP_REST_Server::EDITABLE;
    public const DELETE = WP_REST_Server::DELETABLE;
}
