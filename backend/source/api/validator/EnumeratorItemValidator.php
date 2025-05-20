<?php

namespace oml\api\enum;

use oml\api\model\EnumeratorItemModel;
use oml\php\abstract\Validator;

class EnumeratorItemValidator extends Validator
{
    public function __construct()
    {
        parent::__construct(EnumeratorItemModel::class);
    }
}
