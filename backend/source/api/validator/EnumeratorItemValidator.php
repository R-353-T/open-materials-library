<?php

namespace oml\api\validator;

use oml\api\model\EnumeratorItemModel;
use oml\api\model\EnumeratorModel;
use oml\api\repository\TypeRepository;
use oml\php\abstract\Validator;

class EnumeratorItemValidator extends Validator
{
    private readonly TypeRepository $typeRepository;

    public function __construct()
    {
        parent::__construct(EnumeratorItemModel::class);
        $this->typeRepository = TypeRepository::inject();
    }

    public function item(int $position, mixed $item, EnumeratorModel $enumerator, array &$enumerator_error_list): void
    {
        $this->model = new EnumeratorItemModel();
        $this->model->position = $position;
        $this->error_list = [];

        $this
            ->initialize("items", $item, null, $position)
            ->validate("oml__required")
            ->validate("oml__array")
            ->assign();

        if ($enumerator->id !== null) {
            $this
                ->initialize("items", ($item["id"] ?? null), "id", $position)
                ->validate("oml__enumerator_item_id", [$enumerator->id])
                ->assign();
        }

        $this
            ->initialize("items", $item["value"] ?? null, "value", $position)
            ->validate("oml__required")
            ->validate("oml__dynamic_value", [$enumerator->typeId])
            ->validate("oml__enumerator_item_value", [$enumerator]);
        if ($this->hasError("items") === false) {
            $type = $this->typeRepository->selectById($enumerator->typeId);
            $this->model->{$type->column} = $this->parameterValue;
        }

        if ($enumerator->quantityId !== null) {
            $this
                ->initialize("items", $item["quantityItemId"] ?? null, "quantityItemId", $position)
                ->validate("oml__required")
                ->validate("oml__quantity_item_id", [$enumerator->quantityId])
                ->assign();
        }

        if ($this->hasError()) {
            array_push($enumerator_error_list, ...$this->error_list);
        } else {
            unset($this->model->items);
            $enumerator->items[] = $this->model;
        }
    }
}
