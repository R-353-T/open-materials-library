<?php

namespace oml\api\model;

class EnumeratorItemModel
{
    public ?int $id = null;
    public int $position;
    public int $enumeratorId;
    public ?int $quantityItemId = null;

    public ?string $text = null;
    public int|float|null $number = null;
}
