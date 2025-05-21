<?php

namespace oml\api\validator;

use oml\api\model\QuantityItemModel;
use oml\api\model\QuantityModel;
use oml\php\abstract\Validator;

class QuantityItemValidator extends Validator
{
    public function __construct()
    {
        parent::__construct(QuantityItemModel::class);
    }

    public function item(int $position, mixed $item, QuantityModel $quantity, array &$quantity_error_list): void
    {
        $this->model = new QuantityItemModel();
        $this->model->position = $position;
        $this->error_list = [];

        $this
            ->initialize("items", $item, null, $position)
            ->validate("oml__required")
            ->validate("oml__array")
            ->assign();

        if ($quantity->id !== null) {
            $this
                ->initialize("items", ($item["id"] ?? null), "id", $position)
                ->validate("oml__quantity_item_id", [$quantity->id])
                ->assign();
        }

        $this
            ->initialize("items", ($item["value"] ?? null), "value", $position)
            ->validate("oml__required")
            ->validate("oml__label")
            ->validate("oml__quantity_item_value", [$quantity])
            ->assign();

        if ($this->hasError()) {
            array_push($quantity_error_list, ...$this->error_list);
        } else {
            unset($this->model->items);
            $quantity->items[] = $this->model;
        }
    }
}
