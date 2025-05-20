<?php

namespace oml\api\validator;

use oml\api\controller\TypeController;
use oml\api\model\TypeModel;
use oml\php\abstract\Validator;
use WP_REST_Request;

class TypeValidator extends Validator
{
    private readonly TypeController $controller;

    public function __construct()
    {
        parent::__construct(TypeModel::class);
        $this->controller = TypeController::inject();
    }

    public function list(WP_REST_Request $request)
    {
        return $this->controller->list();
    }
}
