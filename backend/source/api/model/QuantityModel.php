<?php

namespace oml\api\model;

class QuantityModel
{
    public ?int $id = null;
    public string $name;
    public string $description;

    public array $items;
}
