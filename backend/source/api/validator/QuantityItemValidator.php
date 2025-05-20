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
            $id = isset($item["id"]) ? $item["id"] : null;

            $this
                ->initialize("items", $id, "id", $position)
                ->validate("oml__quantity_item_id", [$quantity->id])
                ->assign();
        }

        $value = isset($item["value"]) ? $item["value"] : null;
        $this
            ->initialize("items", $value, "value", $position)
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
