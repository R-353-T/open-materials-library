<?php

namespace oml\api\model;

use oml\php\abstract\Model;

class QuantityModel extends Model
{
    public string $description;

    /** @var QuantityItemModel[]|ValueModel[] */
    public array $items;

    public string $name;
}
