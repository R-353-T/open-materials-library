<?php

namespace oml\api\model;

use oml\api\model\QuantityItemModel;

class ValueModel
{
    public ?int $id = null;
    public mixed $value;

    public static function fromQuantityItem(QuantityItemModel $item): ValueModel
    {
        $value = new ValueModel();
        $value->id = $item->id;
        $value->value = $item->value;
        return $value;
    }

    public static function fromEnumeratorItem(EnumeratorItemModel $item): ValueModel
    {
        $value = new ValueModel();
        $value->id = $item->id;
        $value->value = $item->text ?? $item->number;
        return $value;
    }
}
