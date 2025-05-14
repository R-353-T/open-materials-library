<?php

namespace oml\api\model;

class EnumeratorModel
{
    public ?int $id = null;
    public string $name;
    public string $description;
    public int $typeId;
    public ?int $quantityId = null;
    public array $items;
}
