<?php

namespace oml\api\model;

use oml\php\abstract\Model;

class EnumeratorItemModel extends Model
{
    public int $enumeratorId;

    public int $position;

    public int|float|null $number = null;

    public ?int $quantityItemId = null;

    public ?string $text = null;
}
