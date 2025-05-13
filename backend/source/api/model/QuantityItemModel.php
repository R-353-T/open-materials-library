<?php

namespace oml\api\model;

class QuantityItemModel
{
    public ?int $id = null;
    public int $quantityId;
    public string $value;
    public int $position;
}
