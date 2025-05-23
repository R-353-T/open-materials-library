<?php

namespace oml\api\model;

use oml\php\abstract\Model;

class EnumeratorModel extends Model
{
    public string $description;

    /** @var EnumeratorItemModel[]|ValueModel[] */
    public array $items;

    public string $name;

    public ?int $quantityId = null;

    public int $typeId;
}
