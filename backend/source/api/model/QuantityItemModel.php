<?php

namespace oml\api\model;

use oml\php\abstract\Model;

class QuantityItemModel extends Model
{
    public int $position;

    public int $quantityId;

    public string $value;
}
